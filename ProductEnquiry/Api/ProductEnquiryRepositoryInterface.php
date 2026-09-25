<?php
namespace Codilar\ProductEnquiry\Api;
use Codilar\ProductEnquiry\Api\Data\ProductEnquiryInterface;

interface ProductEnquiryRepositoryInterface
{
    public function save(ProductEnquiryInterface $productEnquiry): ProductEnquiryInterface;
    public function deleteById(int $enquiryId): bool;
    public function deleteByIds(array $enquiryIds): bool;
}
