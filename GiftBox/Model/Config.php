<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const string XML_PATH_ENABLED = 'giftbox/general/enabled';
    private const string XML_PATH_REQUIRED_COFFEES = 'giftbox/general/required_coffees';
    private const string XML_PATH_DISCOUNT = 'giftbox/general/discount';
    private const string XML_PATH_GIFT_MESSAGE_MAX_LENGTH = 'giftbox/general/gift_message_max_length';
    private const string XML_PATH_COFFEE_CATEGORY = 'giftbox/general/coffee_category';
    private const string XML_PATH_EQUIPMENT_CATEGORY = 'giftbox/general/equipment_category';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getRequiredCoffeeCount(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_REQUIRED_COFFEES,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return (float) $this->scopeConfig->getValue(
            self::XML_PATH_DISCOUNT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getGiftMessageMaxLength(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_GIFT_MESSAGE_MAX_LENGTH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getCoffeeCategoryId(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_COFFEE_CATEGORY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getEquipmentCategoryId(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_EQUIPMENT_CATEGORY,
            ScopeInterface::SCOPE_STORE
        );
    }
}
