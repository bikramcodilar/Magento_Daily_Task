<?php

namespace Codilar\ProductEnquiryREST\Model;

use Codilar\ProductEnquiryREST\Api\Data\ProductEnquiryInterface;
use Codilar\ProductEnquiryREST\Model\ResourceModel\ProductEnquiry as ProductEnquiryResource;
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
        return $this->getData('enquiry_id');
    }

    /**
     * @param $enquiryId
     * @return ProductEnquiryInterface
     */
    public function setEnquiryId($enquiryId): ProductEnquiryInterface
    {
        return $this->setData('enquiry_id', $enquiryId);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getData('name');
    }

    /**
     * @param $name
     * @return ProductEnquiryInterface
     */
    public function setName($name): ProductEnquiryInterface
    {
        return $this->setData('name', $name);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->getData('email');
    }

    /**
     * @param $email
     * @return ProductEnquiryInterface
     */
    public function setEmail($email): ProductEnquiryInterface
    {
        return $this->setData('email', $email);
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->getData('address');
    }

    /**
     * @param $address
     * @return ProductEnquiryInterface
     */
    public function setAddress($address): ProductEnquiryInterface
    {
        return $this->setData('address', $address);
    }

    /**
     * @return float
     */
    public function getQuantity(): float
    {
        return $this->getData('quantity');
    }

    /**
     * @param $quantity
     * @return ProductEnquiryInterface
     */
    public function setQuantity($quantity): ProductEnquiryInterface
    {
        return $this->setData('quantity', $quantity);
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->getData('sku');
    }

    /**
     * @param $sku
     * @return ProductEnquiryInterface
     */
    public function setSku($sku): ProductEnquiryInterface
    {
        return $this->setData('sku', $sku);
    }
}
