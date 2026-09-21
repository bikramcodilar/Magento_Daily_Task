<?php

declare(strict_types=1);
namespace Codilar\GiftBox\Plugin\Quote\Item;

use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Api\Data\OrderItemInterface;

class ToOrderItemPlugin
{
    public function afterConvert(
        ToOrderItem $subject,
        OrderItemInterface $orderItem,
        Item $item,
        array $data = []
    ): OrderItemInterface {
        $giftBoxData = $item->getData('giftbox_data');
        if ($giftBoxData !== null && $giftBoxData !== '') {
            $orderItem->setData(
                'giftbox_data',
                $giftBoxData
            );
        }
        return $orderItem;
    }
}
