<?php
namespace Codilar\ProductEnquiry\Api\Data;

interface ProductEnquiryInterface
{
    public const string ENQUIRY_ID = 'enquiry_id';
    public const string NAME = 'name';
    public const string EMAIL = 'email';
    public const string SKU = 'sku';
    public const string QUANTITY = 'quantity';
    public function getEnquiryId(): ?int;
    public function setEnquiryId(int $enquiryId): self;
    public function getName(): string;
    public function setName(string $name): self;
    public function getEmail(): string;
    public function setEmail(string $email): self;
    public function getSku(): string;
    public function setSku(string $sku): self;
    public function getQuantity(): float;
    public function setQuantity(float $quantity): self;
}
