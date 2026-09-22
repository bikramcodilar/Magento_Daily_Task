<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Plugin\Checkout;

use Magento\Checkout\Model\Cart;

class CartQuantityPlugin
{
    private const string GIFT_BOX_SKU = 'gift-box';

    /**
     * @param Cart $subject
     * @param array $data
     * @return array[]
     */
    public function beforeUpdateItems(
        Cart $subject,
        array $data
    ): array {
        foreach ($data as $itemId => &$itemData) {
            $item = $subject->getQuote()->getItemById((int) $itemId);
            if (!$item) {
                continue;
            }
            if ($item->getSku() !== self::GIFT_BOX_SKU) {
                continue;
            }
            $itemData['qty'] = 1;
        }
        unset($itemData);
        return [$data];
    }
}
