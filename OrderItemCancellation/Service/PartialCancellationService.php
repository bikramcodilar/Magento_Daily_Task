<?php
declare(strict_types=1);

namespace Codilar\OrderItemCancellation\Service;

use Codilar\OrderItemCancellation\Api\CancellationRequestRepositoryInterface;
use Codilar\OrderItemCancellation\Api\ItemCancellationRepositoryInterface;
use Codilar\OrderItemCancellation\Model\CancellationRequestFactory;
use Codilar\OrderItemCancellation\Model\ItemCancellationFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\OrderMutexInterface;

class PartialCancellationService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CancellationRequestRepositoryInterface $requestRepository,
        private readonly ItemCancellationRepositoryInterface $itemCancellationRepository,
        private readonly CancellationRequestFactory $cancellationRequestFactory,
        private readonly ItemCancellationFactory $itemCancellationFactory,
        private readonly CancellationValidator $validator,
        private readonly CancellationCalculator $calculator,
        private readonly OrderCancellationManager $orderCancellationManager,
        private readonly InventoryReservationCompensator $inventoryReservationCompensator,
        private readonly OrderMutexInterface $orderMutex,
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    /**
     * @param int $orderId
     * @param int $customerId
     * @param array $items
     * @param string $requestToken
     * @return void
     * @throws LocalizedException
     */
    public function cancel(
        int $orderId,
        int $customerId,
        array $items,
        string $requestToken
    ): void {
        if ($orderId <= 0) {
            throw new LocalizedException(
                __('Invalid order.')
            );
        }
        if ($customerId <= 0) {
            throw new LocalizedException(
                __('Invalid customer.')
            );
        }
        if (!preg_match('/^[a-f0-9]{64}$/', $requestToken)) {
            throw new LocalizedException(
                __('Invalid cancellation request.')
            );
        }
        $requestedQuantities = $this->prepareRequestedQuantities($items);
        if ($requestedQuantities === []) {
            throw new LocalizedException(
                __('Please select at least one item to cancel.')
            );
        }
        $this->orderMutex->execute(
            $orderId,
            function () use (
                $orderId,
                $customerId,
                $requestedQuantities,
                $requestToken
            ): void {
                $this->processCancellation(
                    $orderId,
                    $customerId,
                    $requestedQuantities,
                    $requestToken
                );
            }
        );
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isOrderEligible(Order $order): bool
    {
        return $this->validator->isOrderEligible($order);
    }

    /**
     * @param array $items
     * @return array
     */
    private function prepareRequestedQuantities(
        array $items
    ): array {
        $result = [];

        foreach ($items as $itemId => $quantity) {
            if (!is_numeric($itemId) || !is_numeric($quantity)) {
                continue;
            }

            $itemId = (int) $itemId;
            $quantity = (float) $quantity;

            if ($itemId <= 0 || $quantity <= 0) {
                continue;
            }

            $result[$itemId] = $quantity;
        }

        return $result;
    }

    /**
     * @param int $orderId
     * @param int $customerId
     * @param array $requestedQuantities
     * @param string $requestToken
     * @return void
     * @throws LocalizedException
     * @throws \Throwable
     */
    private function processCancellation(
        int $orderId,
        int $customerId,
        array $requestedQuantities,
        string $requestToken
    ): void {
        $order = $this->orderRepository->get($orderId);
        $this->validator->validateOrder(
            $order,
            $customerId
        );

        if ($this->requestExists($requestToken)) {
            return;
        }
        $orderItems = $this->getOrderItemsById($order);
        foreach ($requestedQuantities as $itemId => $requestedQty) {
            if (!isset($orderItems[$itemId])) {
                throw new LocalizedException(
                    __('The selected item does not belong to this order.')
                );
            }
            $this->validator->validateItem(
                $orderItems[$itemId],
                $requestedQty
            );
        }
        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();
        try {

            $request = $this->cancellationRequestFactory->create();
            $request->setData([
                'request_token' => $requestToken,
                'order_id' => (int) $order->getId(),
                'order_increment_id' => (string) $order->getIncrementId(),
                'customer_id' => $customerId,
                'grand_total' => 0,
                'base_grand_total' => 0,
            ]);
            try {
                $this->requestRepository->save($request);
            } catch (AlreadyExistsException $exception) {
                if ($this->requestExists($requestToken)) {
                    $connection->rollBack();
                    return;
                }
                throw $exception;
            }
            $totalAmounts = [
                'row_total' => 0.0,
                'base_row_total' => 0.0,
                'tax_amount' => 0.0,
                'base_tax_amount' => 0.0,
                'discount_amount' => 0.0,
                'base_discount_amount' => 0.0,
                'grand_total' => 0.0,
                'base_grand_total' => 0.0,
            ];
            foreach ($requestedQuantities as $itemId => $requestedQty) {
                $orderItem = $orderItems[$itemId];
                $amounts = $this->calculator->calculate(
                    $orderItem,
                    $requestedQty
                );
                $itemCancellation =
                    $this->itemCancellationFactory->create();
                $itemCancellation->setData([
                    'request_id' => (int) $request->getId(),
                    'order_item_id' => (int) $orderItem->getId(),
                    'sku' => (string) $orderItem->getSku(),
                    'product_name' => (string) $orderItem->getName(),
                    'qty_cancelled' => $requestedQty,
                    'row_total' => $amounts['row_total'],
                    'base_row_total' => $amounts['base_row_total'],
                    'tax_amount' => $amounts['tax_amount'],
                    'base_tax_amount' => $amounts['base_tax_amount'],
                    'discount_amount' => $amounts['discount_amount'],
                    'base_discount_amount' => $amounts['base_discount_amount'],
                    'grand_total' => $amounts['grand_total'],
                    'base_grand_total' => $amounts['base_grand_total'],
                ]);
                $this->itemCancellationRepository->save(
                    $itemCancellation
                );
                $this->orderCancellationManager->applyItemCancellation(
                    $orderItem,
                    $requestedQty,
                    $amounts
                );
                $this->orderCancellationManager->applyOrderCancellation(
                    $order,
                    $amounts
                );
                $this->inventoryReservationCompensator->compensate(
                    $orderItem,
                    $requestedQty
                );
                $this->addAmounts(
                    $totalAmounts,
                    $amounts
                );
            }
            $request->setGrandTotal(
                $totalAmounts['grand_total']
            );
            $request->setBaseGrandTotal(
                $totalAmounts['base_grand_total']
            );
            $this->requestRepository->save($request);

            $this->orderRepository->save($order);

            $connection->commit();
        } catch (\Throwable $exception) {
            if ($connection->getTransactionLevel() > 0) {
                $connection->rollBack();
            }
            throw $exception;
        }
        $order->addCommentToStatusHistory(
            __(
                'Partial cancellation request %1 was processed.',
                $requestToken
            )
        );
        $this->orderRepository->save($order);
    }

    /**
     * @param Order $order
     * @return array
     */
    private function getOrderItemsById(Order $order): array
    {
        $items = [];
        foreach ($order->getAllItems() as $item) {
            $items[(int) $item->getId()] = $item;
        }
        return $items;
    }

    /**
     * @param string $requestToken
     * @return bool
     */
    private function requestExists(string $requestToken): bool
    {
        try {
            $this->requestRepository->getByToken($requestToken);
            return true;
        } catch (NoSuchEntityException) {
            return false;
        }
    }

    /**
     * @param array $totalAmounts
     * @param array $amounts
     * @return void
     */
    private function addAmounts(
        array &$totalAmounts,
        array $amounts
    ): void {
        $totalAmounts['row_total'] += $amounts['row_total'];
        $totalAmounts['base_row_total'] += $amounts['base_row_total'];

        $totalAmounts['tax_amount'] += $amounts['tax_amount'];
        $totalAmounts['base_tax_amount'] += $amounts['base_tax_amount'];

        $totalAmounts['discount_amount'] += $amounts['discount_amount'];
        $totalAmounts['base_discount_amount'] +=
            $amounts['base_discount_amount'];

        $totalAmounts['grand_total'] += $amounts['grand_total'];
        $totalAmounts['base_grand_total'] +=
            $amounts['base_grand_total'];
    }
}
