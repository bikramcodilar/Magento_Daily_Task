<?php

namespace Codilar\OrderItemCancellation\Block;

use Codilar\OrderItemCancellation\Service\PartialCancellationService;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;

class CancelItems extends Template
{
    private ?string $requestToken = null;

    public function __construct(
        Template\Context $context,
        private readonly Registry $registry,
        private readonly PartialCancellationService $partialCancellationService,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getOrder(): ?Order
    {
        // $order = $this->registry->registry('current_order');

        return $order instanceof Order ? $order : null;
    }

    public function canShowCancellationForm(): bool
    {
        $order = $this->getOrder();

        return $order
            && $this->partialCancellationService->isOrderEligible($order)
            && !empty($this->getCancelableItems());
    }

    public function getCancelableItems(): array
    {
        $order = $this->getOrder();

        if (!$order) {
            return [];
        }

        $items = [];

        foreach ($order->getAllItems() as $item) {
            if (
                $item->getParentItemId()
                || !in_array($item->getProductType(), ['simple', 'virtual'], true)
                || (float) $item->getQtyToCancel() <= 0
            ) {
                continue;
            }

            $items[] = $item;
        }

        return $items;
    }

    public function getRequestToken(): string
    {
        if ($this->requestToken === null) {
            $this->requestToken = bin2hex(random_bytes(32));
        }

        return $this->requestToken;
    }

    public function getSubmitUrl(): string
    {
        return $this->getUrl('orderitemcancel/cancel/submit');
    }
}
