<?php
namespace Codilar\ProductEnquiry\Model;

use Codilar\ProductEnquiry\Api\Data\ProductEnquiryInterface;
use Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry as ProductEnquiryResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class ProductEnquiry extends AbstractModel implements ProductEnquiryInterface
{
    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(ProductEnquiryResource::class);
    }

    /**
     * @return int|null
     */
    public function getEnquiryId(): ?int
    {
        $id = $this->getData(self::ENQUIRY_ID);
        return $id ? (int) $id : null;
    }

    /**
     * @param int $enquiryId
     * @return self
     */
    public function setEnquiryId(int $enquiryId): self
    {
        return $this->setData(self::ENQUIRY_ID, $enquiryId);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return (string) $this->getData(self::EMAIL);
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return (string) $this->getData(self::SKU);
    }

    /**
     * @param string $sku
     * @return self
     */
    public function setSku(string $sku): self
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @return float
     */
    public function getQuantity(): float
    {
        return (float) $this->getData(self::QUANTITY);
    }

    /**
     * @param float $quantity
     * @return self
     */
    public function setQuantity(float $quantity): self
    {
        return $this->setData(self::QUANTITY, $quantity);
    }
}
