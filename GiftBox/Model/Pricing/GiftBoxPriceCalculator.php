<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model\Pricing;

use Codilar\GiftBox\Api\Data\GiftBoxPriceResultInterface;
use Codilar\GiftBox\Model\Config;
use Codilar\GiftBox\Model\Data\GiftBoxPriceResultFactory;

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
            $componentPrices[$product->getSku()] = $price;
            $componentSubtotal += $price;
        }
        return $this->priceResultFactory->create()
            ->setComponentSubtotal($componentSubtotal)
            ->setComponentPrices($componentPrices);
    }
}
