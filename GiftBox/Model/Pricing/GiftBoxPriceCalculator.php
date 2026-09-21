<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model\Pricing;

use Codilar\GiftBox\Api\Data\GiftBoxPriceResultInterface;
use Codilar\GiftBox\Model\Config;
use Codilar\GiftBox\Model\Data\GiftBoxPriceResultFactory;
use Magento\Catalog\Api\Data\ProductInterface;

class GiftBoxPriceCalculator
{
    public function __construct(
        private readonly Config $config,
        private readonly GiftBoxPriceResultFactory $priceResultFactory
    ) {
    }

    /**
     * @param array $products
     * @return GiftBoxPriceResultInterface
     */
    public function calculate(
        array $products
    ): GiftBoxPriceResultInterface {
        $componentSubtotal = 0.0;
        $componentPrices = [];
        foreach ($products as $product) {
            $price = (float) $product->getFinalPrice(1);
            $componentSubtotal += $price;
            $componentPrices[$product->getSku()] = $price;
        }
        $discount = $this->config->getDiscount();
        $giftBoxPrice = max(
            0.0,
            $componentSubtotal - $discount
        );
        return $this->priceResultFactory->create()
            ->setComponentSubtotal($componentSubtotal)
            ->setDiscount($discount)
            ->setGiftBoxPrice($giftBoxPrice)
            ->setComponentPrices($componentPrices);
    }
}
