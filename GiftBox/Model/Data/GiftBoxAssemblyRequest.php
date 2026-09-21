<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model\Data;

use Codilar\GiftBox\Api\Data\GiftBoxAssemblyRequestInterface;

class GiftBoxAssemblyRequest implements GiftBoxAssemblyRequestInterface
{
    /**
     * @var array
     */
    private array $coffeeSkus = [];
    /**
     * @var string|null
     */
    private ?string $equipmentSku = null;
    /**
     * @var string|null
     */
    private ?string $giftMessage = null;

    /**
     * @return array
     */
    public function getCoffeeSkus(): array
    {
        return $this->coffeeSkus;
    }

    /**
     * @param array $coffeeSkus
     * @return $this
     */
    public function setCoffeeSkus(array $coffeeSkus): self
    {
        $this->coffeeSkus = $coffeeSkus;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEquipmentSku(): ?string
    {
        return $this->equipmentSku;
    }

    /**
     * @param string|null $equipmentSku
     * @return $this
     */
    public function setEquipmentSku(?string $equipmentSku): self
    {
        $this->equipmentSku = $equipmentSku;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGiftMessage(): ?string
    {
        return $this->giftMessage;
    }

    /**
     * @param string|null $giftMessage
     * @return $this
     */
    public function setGiftMessage(?string $giftMessage): self
    {
        $this->giftMessage = $giftMessage;
        return $this;
    }
}
