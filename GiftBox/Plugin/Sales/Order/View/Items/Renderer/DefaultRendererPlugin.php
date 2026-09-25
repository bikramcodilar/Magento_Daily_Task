<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Plugin\Sales\Order\View\Items\Renderer;

use Codilar\GiftBox\Block\Adminhtml\Order\GiftBox;
use Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer;
use Magento\Sales\Model\Order\Item;

class DefaultRendererPlugin
{
    public function __construct(
        private readonly GiftBox $giftBoxBlock
    ) {
    }

    public function afterToHtml(
        DefaultRenderer $subject,
        string $result
    ): string {
        $item = $subject->getItem();

        if (!$item instanceof Item) {
            return $result;
        }

        if ($item->getSku() !== 'gift-box') {
            return $result;
        }

        $giftBoxData = $this->giftBoxBlock
            ->setItem($item)
            ->getGiftBoxData();

        if ($giftBoxData === []) {
            return $result;
        }

        return $result . $this->giftBoxBlock->toHtml();
    }
}
