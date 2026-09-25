<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Plugin\Sales\Order;

use Codilar\GiftBox\Model\Order\GiftBoxContentProvider;
use Magento\Framework\Escaper;
use Magento\Sales\Block\Order\Items;
use Magento\Sales\Model\Order\Item;

class ItemsPlugin
{
    public function __construct(
        private readonly GiftBoxContentProvider $giftBoxContentProvider,
        private readonly Escaper $escaper
    ) {
    }

    /**
     * @param Items $subject
     * @param string $result
     * @param Item $item
     * @return string
     */
    public function afterGetItemHtml(
        Items $subject,
        string $result,
        Item $item
    ): string {
        if ($item->getSku() !== 'gift-box') {
            return $result;
        }
        $giftBoxData = $this->giftBoxContentProvider->getData($item);
        if ($giftBoxData === []) {
            return $result;
        }
        $coffeeNames = $this->giftBoxContentProvider->getCoffeeNames($item);
        $equipmentName = $this->giftBoxContentProvider->getEquipmentName($item);
        $giftMessage = $this->giftBoxContentProvider->getGiftMessage($item);
        $html = '<tr class="giftbox-order-details">';
        $html .= '<td colspan="5">';
        $html .= '<div class="giftbox-order-content">';
        $html .= '<strong>';
        $html .= $this->escaper->escapeHtml(
            __('Gift Box Contents')
        );
        $html .= '</strong>';
        if (!empty($coffeeNames)) {
            $html .= '<div class="giftbox-order-section">';
            $html .= '<strong>';
            $html .= $this->escaper->escapeHtml(
                __('Coffees')
            );
            $html .= '</strong>';
            $html .= '<ul>';
            foreach ($coffeeNames as $coffeeName) {
                $html .= '<li>';
                $html .= $this->escaper->escapeHtml($coffeeName);
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        if ($equipmentName !== null && $equipmentName !== '') {
            $html .= '<div class="giftbox-order-section">';
            $html .= '<strong>';
            $html .= $this->escaper->escapeHtml(
                __('Equipment')
            );
            $html .= '</strong>';
            $html .= '<div>';
            $html .= $this->escaper->escapeHtml($equipmentName);
            $html .= '</div>';
            $html .= '</div>';
        }
        if ($giftMessage !== null && trim($giftMessage) !== '') {
            $html .= '<div class="giftbox-order-section">';
            $html .= '<strong>';
            $html .= $this->escaper->escapeHtml(
                __('Gift Message')
            );
            $html .= '</strong>';
            $html .= '<div>';
            $html .= $this->escaper->escapeHtml($giftMessage);
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</td>';
        $html .= '</tr>';

        return $result . $html;
    }
}
