<?php
namespace Codilar\ProductEnquiry\Api;
use Codilar\ProductEnquiry\Api\Data\ProductEnquiryInterface;

interface ProductEnquiryRepositoryInterface
{
    /**
     * @param ProductEnquiryInterface $productEnquiry
     * @return ProductEnquiryInterface
     */
    public function save(ProductEnquiryInterface $productEnquiry): ProductEnquiryInterface;

    /**
     * @param int $enquiryId
     * @return ProductEnquiryInterface
     */
    public function getById(int $enquiryId): ProductEnquiryInterface;

    /**
     * @param int $enquiryId
     * @return bool
     */
    public function deleteById(int $enquiryId): bool;

    /**
     * @param array $enquiryIds
     * @return bool
     */
    public function deleteByIds(array $enquiryIds): bool;
}
