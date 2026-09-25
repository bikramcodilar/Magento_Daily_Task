<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Model\Order;

use Codilar\GiftBox\Model\Quote\GiftBoxQuoteDataReader;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Sales\Model\Order\Item;

class GiftBoxContentProvider
{
    public function __construct(
        private readonly GiftBoxQuoteDataReader $giftBoxQuoteDataReader,
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    public function getData(Item $item): array
    {
        if ($item->getSku() !== 'gift-box') {
            return [];
        }

        return $this->giftBoxQuoteDataReader->get($item);
    }

    public function getCoffeeNames(Item $item): array
    {
        $data = $this->getData($item);

        $names = [];

        foreach ($data['coffee_skus'] ?? [] as $sku) {
            $names[] = $this->getProductName((string) $sku);
        }

        return $names;
    }

    public function getEquipmentName(Item $item): ?string
    {
        $data = $this->getData($item);

        $sku = $data['equipment_sku'] ?? null;

        if (!$sku) {
            return null;
        }

        return $this->getProductName((string) $sku);
    }

    public function getGiftMessage(Item $item): ?string
    {
        $data = $this->getData($item);

        $message = $data['gift_message'] ?? null;

        return $message !== null
            ? (string) $message
            : null;
    }

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
