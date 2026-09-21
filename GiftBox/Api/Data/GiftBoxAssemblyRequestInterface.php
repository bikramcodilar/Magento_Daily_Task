<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Api\Data;

interface GiftBoxAssemblyRequestInterface
{
    /**
     * @return array
     */
    public function getCoffeeSkus(): array;

    /**
     * @param array $coffeeSkus
     * @return self
     */
    public function setCoffeeSkus(array $coffeeSkus): self;

    /**
     * @return string|null
     */
    public function getEquipmentSku(): ?string;

    /**
     * @param string|null $equipmentSku
     * @return self
     */
    public function setEquipmentSku(?string $equipmentSku): self;

    /**
     * @return string|null
     */
    public function getGiftMessage(): ?string;

    /**
     * @param string|null $giftMessage
     * @return self
     */
    public function setGiftMessage(?string $giftMessage): self;
}
