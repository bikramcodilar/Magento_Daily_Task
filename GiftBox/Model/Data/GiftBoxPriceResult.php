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
