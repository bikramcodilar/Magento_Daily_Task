<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Plugin\Sales\Order;

use Codilar\GiftBox\Model\Quote\GiftBoxQuoteDataReader;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Escaper;
use Magento\Sales\Block\Order\Items;
use Magento\Sales\Model\Order\Item;

class ItemsPlugin
{
    public function __construct(
        private readonly GiftBoxQuoteDataReader $giftBoxQuoteDataReader,
        private readonly ProductRepositoryInterface $productRepository,
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
        $giftBoxData = $this->giftBoxQuoteDataReader->get($item);
        if ($giftBoxData === []) {
            return $result;
        }
        $coffeeSkus = $giftBoxData['coffee_skus'] ?? [];
        $equipmentSku = $giftBoxData['equipment_sku'] ?? null;
        $giftMessage = $giftBoxData['gift_message'] ?? null;
        $html = '<tr class="giftbox-order-details">';
        $html .= '<td colspan="5">';
        $html .= '<div class="giftbox-order-content">';
        $html .= '<strong>';
        $html .= $this->escaper->escapeHtml(
            __('Gift Box Contents')
        );
        $html .= '</strong>';
        if (!empty($coffeeSkus)) {
            $html .= '<div class="giftbox-order-section">';
            $html .= '<strong>';
            $html .= $this->escaper->escapeHtml(
                __('Coffees')
            );
            $html .= '</strong>';
            $html .= '<ul>';
            foreach ($coffeeSkus as $sku) {
                $html .= '<li>';
                $html .= $this->escaper->escapeHtml(
                    $this->getProductName((string) $sku)
                );
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        if ($equipmentSku !== null && $equipmentSku !== '') {
            $html .= '<div class="giftbox-order-section">';
            $html .= '<strong>';
            $html .= $this->escaper->escapeHtml(
                __('Equipment')
            );
            $html .= '</strong>';
            $html .= '<div>';
            $html .= $this->escaper->escapeHtml(
                $this->getProductName((string) $equipmentSku)
            );
            $html .= '</div>';
            $html .= '</div>';
        }
        if ($giftMessage !== null && trim((string) $giftMessage) !== '') {
            $html .= '<div class="giftbox-order-section">';

            $html .= '<strong>';
            $html .= $this->escaper->escapeHtml(
                __('Gift Message')
            );
            $html .= '</strong>';

            $html .= '<div>';
            $html .= $this->escaper->escapeHtml(
                (string) $giftMessage
            );
            $html .= '</div>';

            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</td>';
        $html .= '</tr>';

        return $result . $html;
    }

    /**
     * @param string $sku
     * @return string
     */
    private function getProductName(string $sku): string
    {
        try {
            return (string) $this->productRepository
                ->get($sku)
                ->getName();
        } catch (\Magento\Framework\Exception\NoSuchEntityException) {
            return $sku;
        }
    }
}
