<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Block;

use Codilar\GiftBox\Model\Config;
use Codilar\GiftBox\Model\GiftBoxProductProvider;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

class GiftBox extends Template
{
    public function __construct(
        Template\Context $context,
        private readonly GiftBoxProductProvider $productProvider,
        private readonly Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return Collection
     * @throws LocalizedException
     */
    public function getCoffeeProducts(): Collection
    {
        return $this->productProvider->getEligibleCoffeeProducts();
    }

    /**
     * @return Collection
     * @throws LocalizedException
     */
    public function getEquipmentProducts(): Collection
    {
        return $this->productProvider->getEligibleEquipmentProducts();
    }

    /**
     * @return int
     */
    public function getRequiredCoffeeCount(): int
    {
        return $this->config->getRequiredCoffeeCount();
    }

    /**
     * @return int
     */
    public function getGiftMessageMaxLength(): int
    {
        return $this->config->getGiftMessageMaxLength();
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->config->getDiscount();
    }
}
