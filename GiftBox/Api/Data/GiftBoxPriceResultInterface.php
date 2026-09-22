<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Api\Data;

interface GiftBoxPriceResultInterface
{
    /**
     * @return float
     */
    public function getComponentSubtotal(): float;

    /**
     * @return array
     */
    public function getComponentPrices(): array;
}
