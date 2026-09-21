<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model\Data;

use Codilar\GiftBox\Api\Data\GiftBoxPriceResultInterface;

class GiftBoxPriceResult implements GiftBoxPriceResultInterface
{
    /**
     * @var float
     */
    private float $componentSubtotal = 0.0;
    /**
     * @var float
     */
    private float $discount = 0.0;
    /**
     * @var float
     */
    private float $giftBoxPrice = 0.0;
    /**
     * @var array
     */
    private array $componentPrices = [];
    /**
     * @return float
     */
    public function getComponentSubtotal(): float
    {
        return $this->componentSubtotal;
    }

    /**
     * @param float $componentSubtotal
     * @return $this
     */
    public function setComponentSubtotal(float $componentSubtotal): self
    {
        $this->componentSubtotal = $componentSubtotal;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     * @return $this
     */
    public function setDiscount(float $discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return float
     */
    public function getGiftBoxPrice(): float
    {
        return $this->giftBoxPrice;
    }

    /**
     * @param float $giftBoxPrice
     * @return $this
     */
    public function setGiftBoxPrice(float $giftBoxPrice): self
    {
        $this->giftBoxPrice = $giftBoxPrice;
        return $this;
    }

    /**
     * @return array
     */
    public function getComponentPrices(): array
    {
        return $this->componentPrices;
    }

    /**
     * @param array $componentPrices
     * @return $this
     */
    public function setComponentPrices(array $componentPrices): self
    {
        $this->componentPrices = $componentPrices;
        return $this;
    }
}
