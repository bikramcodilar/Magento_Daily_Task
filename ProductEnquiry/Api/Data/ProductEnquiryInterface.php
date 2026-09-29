<?php
namespace Codilar\ProductEnquiry\Api\Data;

interface ProductEnquiryInterface
{
    public const string ENQUIRY_ID = 'enquiry_id';
    public const string NAME = 'name';
    public const string EMAIL = 'email';
    public const string SKU = 'sku';
    public const string QUANTITY = 'quantity';

    /**
     * @return int|null
     */
    public function getEnquiryId(): ?int;

    /**
     * @param int $enquiryId
     * @return self
     */
    public function setEnquiryId(int $enquiryId): self;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self;

    /**
     * @return string
     */
    public function getSku(): string;

    /**
     * @param string $sku
     * @return self
     */
    public function setSku(string $sku): self;

    /**
     * @return float
     */
    public function getQuantity(): float;

    /**
     * @param float $quantity
     * @return self
     */
    public function setQuantity(float $quantity): self;
}
