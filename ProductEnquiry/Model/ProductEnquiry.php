<?php

namespace Codilar\ProductEnquiry\Model;

use Codilar\ProductEnquiry\Api\Data\ProductEnquiryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class ProductEnquiry extends AbstractModel implements ProductEnquiryInterface
{
    /**
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(\Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry::class);
    }

    public function getEnquiryId(): ?int
    {
        $id = $this->getData(self::ENQUIRY_ID);
        return $id ? (int) $id : null;
    }
    public function setEnquiryId(int $enquiryId): self
    {
        return $this->setData(self::ENQUIRY_ID, $enquiryId);
    }

    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    public function getEmail(): string
    {
        return (string) $this->getData(self::EMAIL);
    }

    public function setEmail(string $email): self
    {
        return $this->setData(self::EMAIL, $email);
    }

    public function getSku(): string
    {
        return (string) $this->getData(self::SKU);
    }

    public function setSku(string $sku): self
    {
        return $this->setData(self::SKU, $sku);
    }

    public function getQuantity(): float
    {
        return (float) $this->getData(self::QUANTITY);
    }

    public function setQuantity(float $quantity): self
    {
        return $this->setData(self::QUANTITY, $quantity);
    }
}
