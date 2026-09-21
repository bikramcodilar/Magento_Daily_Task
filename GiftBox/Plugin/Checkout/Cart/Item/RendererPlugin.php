<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Plugin\Checkout\Cart\Item;

use Magento\Checkout\Block\Cart\Item\Renderer;
use Magento\Quote\Model\Quote\Item;

class RendererPlugin
{
    public function afterGetProductAdditionalInformationBlock(
        Renderer $subject,
        $result
    ) {
        $item = $subject->getItem();

        if (!$item instanceof Item) {
            return $result;
        }

        if ($item->getSku() !== 'gift-box') {
            return $result;
        }

        $giftBoxBlock = $subject->getChildBlock(
            'giftbox_details'
        );

        if (!$giftBoxBlock) {
            return $result;
        }

        return $giftBoxBlock->setItem($item);
    }
}
