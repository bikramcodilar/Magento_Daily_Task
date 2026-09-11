<?php
declare(strict_types=1);
namespace Codilar\OrderItemCancellation\Block;

use Codilar\OrderItemCancellation\Service\PartialCancellationService;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Random\RandomException;

class CancelItems extends Template
{
    private ?string $requestToken = null;
    private ?Order $order = null;

    /**
     * @param Template\Context $context
     * @param PartialCancellationService $partialCancellationService
     * @param OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        private readonly PartialCancellationService $partialCancellationService,
        private readonly OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        if ($this->order !== null) {
            return $this->order;
        }
        $orderId = (int) $this->getRequest()->getParam('order_id');
        if ($orderId <= 0) {
            return null;
        }
        try {
            $this->order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException) {
            return null;
        }
        return $this->order;
    }

    /**
     * @return bool
     */
    public function canShowCancellationForm(): bool
    {
        $order = $this->getOrder();
        return $order !== null
            && $this->partialCancellationService->isOrderEligible($order)
            && !empty($this->getCancelableItems());
    }

    /**
     * @return array
     */
    public function getCancelableItems(): array
    {
        $order = $this->getOrder();
        if ($order === null) {
            return [];
        }
        $items = [];
        foreach ($order->getAllItems() as $item) {
            if (
                $item->getParentItemId()
                || !in_array(
                    $item->getProductType(),
                    ['simple', 'virtual'],
                    true
                )
                || (float) $item->getQtyToCancel() <= 0
            ) {
                continue;
            }
            $items[] = $item;
        }
        return $items;
    }

    /**
     * @return string
     * @throws RandomException
     */
    public function getRequestToken(): string
    {
        if ($this->requestToken === null) {
            $this->requestToken = bin2hex(
                random_bytes(32)
            );
        }
        return $this->requestToken;
    }

    /**
     * @return string
     */
    public function getSubmitUrl(): string
    {
        return $this->getUrl(
            'orderitemcancel/cancel/submit'
        );
    }
}
