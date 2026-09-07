<?php

namespace Codilar\OrderItemCancellation\Service;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\OrderMutexInterface;

class PartialCancellationService
{
    private const ELIGIBLE_STATES = [
        Order::STATE_NEW,
        Order::STATE_PENDING_PAYMENT,
        Order::STATE_PROCESSING
    ];

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ResourceConnection $resourceConnection,
        private readonly OrderMutexInterface $orderMutex,
        private readonly InventoryReservationCompensator $inventoryReservationCompensator,
        private readonly EventManager $eventManager
    ) {
    }

    public function isOrderEligible(Order $order): bool
    {
        return in_array($order->getState(), self::ELIGIBLE_STATES, true);
    }

    /**
     * @param array<string, mixed> $requestedItems
     */
    public function cancel(
        int $orderId,
        int $customerId,
        array $requestedItems,
        string $requestToken
    ): void {
        if ($orderId <= 0 || $customerId <= 0) {
            throw new LocalizedException(__('Invalid cancellation request.'));
        }

        if (!preg_match('/^[a-f0-9]{64}$/', $requestToken)) {
            throw new LocalizedException(__('Invalid cancellation request token.'));
        }

        $requestedQuantities = $this->prepareRequestedQuantities($requestedItems);

        if (empty($requestedQuantities)) {
            throw new LocalizedException(
                __('Please enter a cancellation quantity for at least one item.')
            );
        }

        $this->orderMutex->execute(
            $orderId,
            function () use ($orderId, $customerId, $requestToken, $requestedQuantities): void {
                $this->processCancellation(
                    $orderId,
                    $customerId,
                    $requestToken,
                    $requestedQuantities
                );
            }
        );
    }

    /**
     * @param array<string, mixed> $requestedItems
     * @return array<int, float>
     */
    private function prepareRequestedQuantities(array $requestedItems): array
    {
        $quantities = [];

        foreach ($requestedItems as $itemId => $quantity) {
            if (!is_numeric($quantity)) {
                continue;
            }

            $itemId = (int) $itemId;
            $quantity = (float) $quantity;

            if ($itemId > 0 && $quantity > 0) {
                $quantities[$itemId] = $quantity;
            }
        }

        return $quantities;
    }

    /**
     * @param array<int, float> $requestedQuantities
     */
    private function processCancellation(
        int $orderId,
        int $customerId,
        string $requestToken,
        array $requestedQuantities
    ): void {
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);

        if ((int) $order->getCustomerId() !== $customerId) {
            throw new LocalizedException(__('You cannot cancel items from this order.'));
        }

        if (!$this->isOrderEligible($order)) {
            throw new LocalizedException(
                __('This order is not eligible for item cancellation.')
            );
        }

        $connection = $this->resourceConnection->getConnection('sales');
        $requestTable = $this->resourceConnection->getTableName(
            'order_cancellation_request'
        );
        $itemHistoryTable = $this->resourceConnection->getTableName(
            'order_item_cancellation'
        );

        $existingRequestId = $connection->fetchOne(
            $connection->select()
                ->from($requestTable, 'request_id')
                ->where('request_token = ?', $requestToken)
        );

        if ($existingRequestId) {
            throw new LocalizedException(
                __('This cancellation request has already been processed.')
            );
        }

        $orderItems = [];

        foreach ($order->getAllItems() as $item) {
            $orderItems[(int) $item->getId()] = $item;
        }

        $itemsToCancel = [];
        $requestGrandTotal = 0.0;
        $requestBaseGrandTotal = 0.0;

        /*
         * Validate every selected item before changing or saving anything.
         */
        foreach ($requestedQuantities as $itemId => $quantity) {
            if (!isset($orderItems[$itemId])) {
                throw new LocalizedException(__('One selected item is invalid.'));
            }

            $item = $orderItems[$itemId];

            if (
                $item->getParentItemId()
                || !in_array($item->getProductType(), ['simple', 'virtual'], true)
            ) {
                throw new LocalizedException(
                    __('Only simple and virtual products can be cancelled.')
                );
            }

            $availableQuantity = (float) $item->getQtyToCancel();

            if ($quantity > $availableQuantity) {
                throw new LocalizedException(
                    __(
                        '%1 can only be cancelled up to %2 quantity.',
                        $item->getName(),
                        $availableQuantity
                    )
                );
            }

            $amounts = $this->calculateItemAmounts($item, $quantity);

            $itemsToCancel[] = [
                'item' => $item,
                'qty' => $quantity,
                'amounts' => $amounts
            ];

            $requestGrandTotal += $amounts['grand_total'];
            $requestBaseGrandTotal += $amounts['base_grand_total'];
        }

        /*
         * A unique token makes a second click/browser retry harmless.
         */
        $connection->insert($requestTable, [
            'request_token' => $requestToken,
            'order_id' => (int) $order->getId(),
            'order_increment_id' => (string) $order->getIncrementId(),
            'customer_id' => $customerId,
            'grand_total' => round($requestGrandTotal, 4),
            'base_grand_total' => round($requestBaseGrandTotal, 4)
        ]);

        $requestId = (int) $connection->lastInsertId($requestTable);

        foreach ($itemsToCancel as $data) {
            /** @var Item $item */
            $item = $data['item'];
            $quantity = $data['qty'];
            $amounts = $data['amounts'];

            $item->setQtyCanceled(
                (float) $item->getQtyCanceled() + $quantity
            );

            $item->setTaxCanceled(
                (float) $item->getTaxCanceled() + $amounts['tax_amount']
            );

            $item->setDiscountTaxCompensationCanceled(
                (float) $item->getDiscountTaxCompensationCanceled()
                + $amounts['discount_tax_compensation_amount']
            );

            $this->addAmountsToOrder($order, $amounts);

            $connection->insert($itemHistoryTable, [
                'request_id' => $requestId,
                'order_item_id' => (int) $item->getId(),
                'sku' => (string) $item->getSku(),
                'product_name' => (string) $item->getName(),
                'qty_cancelled' => $quantity,
                'row_total' => $amounts['row_total'],
                'base_row_total' => $amounts['base_row_total'],
                'tax_amount' => $amounts['tax_amount'],
                'base_tax_amount' => $amounts['base_tax_amount'],
                'discount_amount' => $amounts['discount_amount'],
                'base_discount_amount' => $amounts['base_discount_amount'],
                'grand_total' => $amounts['grand_total'],
                'base_grand_total' => $amounts['base_grand_total']
            ]);

            /*
             * Do not dispatch Magento's sales_order_item_cancel event here.
             * That event assumes the entire remaining quantity is cancelled.
             */
            $this->inventoryReservationCompensator->compensate(
                $item,
                $quantity
            );
        }

        $order->addStatusHistoryComment(
            __('Customer cancelled selected item quantities. Request #%1', $requestId),
            false
        );

        /*
         * If every supported item is now fully cancelled, mark the order cancelled.
         * This only happens when no part of the order was invoiced or shipped.
         */
        if ($this->isEntireOrderCancelled($order)) {
            $this->finalizeEntireOrderCancellation($order);
        }

        $this->orderRepository->save($order);
    }

    /**
     * @return array<string, float>
     */
    private function calculateItemAmounts(Item $item, float $quantity): array
    {
        $orderedQuantity = (float) $item->getQtyOrdered();

        if ($orderedQuantity <= 0) {
            throw new LocalizedException(__('Invalid order item quantity.'));
        }

        $factor = $quantity / $orderedQuantity;

        $rowTotal = $this->proRate((float) $item->getRowTotal(), $factor);
        $baseRowTotal = $this->proRate((float) $item->getBaseRowTotal(), $factor);

        $taxAmount = $this->proRate((float) $item->getTaxAmount(), $factor);
        $baseTaxAmount = $this->proRate((float) $item->getBaseTaxAmount(), $factor);

        $discountAmount = $this->proRate(
            (float) $item->getDiscountAmount(),
            $factor
        );

        $baseDiscountAmount = $this->proRate(
            (float) $item->getBaseDiscountAmount(),
            $factor
        );

        $discountTaxCompensationAmount = $this->proRate(
            (float) $item->getDiscountTaxCompensationAmount(),
            $factor
        );

        $baseDiscountTaxCompensationAmount = $this->proRate(
            (float) $item->getBaseDiscountTaxCompensationAmount(),
            $factor
        );

        return [
            'row_total' => $rowTotal,
            'base_row_total' => $baseRowTotal,
            'tax_amount' => $taxAmount,
            'base_tax_amount' => $baseTaxAmount,
            'discount_amount' => $discountAmount,
            'base_discount_amount' => $baseDiscountAmount,
            'discount_tax_compensation_amount' => $discountTaxCompensationAmount,
            'base_discount_tax_compensation_amount' => $baseDiscountTaxCompensationAmount,
            'grand_total' => round(
                $rowTotal
                + $taxAmount
                + $discountTaxCompensationAmount
                - $discountAmount,
                4
            ),
            'base_grand_total' => round(
                $baseRowTotal
                + $baseTaxAmount
                + $baseDiscountTaxCompensationAmount
                - $baseDiscountAmount,
                4
            )
        ];
    }

    /**
     * @param array<string, float> $amounts
     */
    private function addAmountsToOrder(Order $order, array $amounts): void
    {
        $order->setSubtotalCanceled(
            (float) $order->getSubtotalCanceled() + $amounts['row_total']
        );

        $order->setBaseSubtotalCanceled(
            (float) $order->getBaseSubtotalCanceled() + $amounts['base_row_total']
        );

        $order->setTaxCanceled(
            (float) $order->getTaxCanceled() + $amounts['tax_amount']
        );

        $order->setBaseTaxCanceled(
            (float) $order->getBaseTaxCanceled() + $amounts['base_tax_amount']
        );

        $order->setDiscountCanceled(
            (float) $order->getDiscountCanceled() + abs($amounts['discount_amount'])
        );

        $order->setBaseDiscountCanceled(
            (float) $order->getBaseDiscountCanceled()
            + abs($amounts['base_discount_amount'])
        );

        $order->setTotalCanceled(
            (float) $order->getTotalCanceled() + $amounts['grand_total']
        );

        $order->setBaseTotalCanceled(
            (float) $order->getBaseTotalCanceled()
            + $amounts['base_grand_total']
        );
    }

    private function isEntireOrderCancelled(Order $order): bool
    {
        $hasSupportedItem = false;

        foreach ($order->getAllItems() as $item) {
            if (
                $item->getParentItemId()
                || !in_array($item->getProductType(), ['simple', 'virtual'], true)
            ) {
                continue;
            }

            $hasSupportedItem = true;

            if (
                (float) $item->getQtyCanceled() < (float) $item->getQtyOrdered()
                || (float) $item->getQtyInvoiced() > 0
                || (float) $item->getQtyShipped() > 0
            ) {
                return false;
            }
        }

        return $hasSupportedItem;
    }

    private function finalizeEntireOrderCancellation(Order $order): void
    {
        /*
         * Same totals approach used by Magento's native full-order cancellation.
         */
        $order->setSubtotalCanceled(
            (float) $order->getSubtotal() - (float) $order->getSubtotalInvoiced()
        );

        $order->setBaseSubtotalCanceled(
            (float) $order->getBaseSubtotal() - (float) $order->getBaseSubtotalInvoiced()
        );

        $order->setTaxCanceled(
            (float) $order->getTaxAmount() - (float) $order->getTaxInvoiced()
        );

        $order->setBaseTaxCanceled(
            (float) $order->getBaseTaxAmount() - (float) $order->getBaseTaxInvoiced()
        );

        $order->setShippingCanceled(
            (float) $order->getShippingAmount()
            - (float) $order->getShippingInvoiced()
        );

        $order->setBaseShippingCanceled(
            (float) $order->getBaseShippingAmount()
            - (float) $order->getBaseShippingInvoiced()
        );

        $order->setDiscountCanceled(
            abs((float) $order->getDiscountAmount())
            - abs((float) $order->getDiscountInvoiced())
        );

        $order->setBaseDiscountCanceled(
            abs((float) $order->getBaseDiscountAmount())
            - abs((float) $order->getBaseDiscountInvoiced())
        );

        $order->setTotalCanceled(
            (float) $order->getGrandTotal() - (float) $order->getTotalPaid()
        );

        $order->setBaseTotalCanceled(
            (float) $order->getBaseGrandTotal()
            - (float) $order->getBaseTotalPaid()
        );

        /*
         * This follows Magento's normal full-order cancellation behavior:
         * void the payment authorization where the payment method supports it.
         */
        $order->getPayment()->cancel();

        $order->setState(Order::STATE_CANCELED);
        $order->setStatus(
            $order->getConfig()->getStateDefaultStatus(Order::STATE_CANCELED)
        );

        $this->eventManager->dispatch('order_cancel_after', ['order' => $order]);
    }

    private function proRate(float $amount, float $factor): float
    {
        return round($amount * $factor, 4);
    }
}
