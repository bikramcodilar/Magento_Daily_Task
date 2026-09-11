<?php
namespace Codilar\ProductEnquiryREST\Api;

use Codilar\ProductEnquiryREST\Api\Data\ApiResponseInterface;
use Codilar\ProductEnquiryREST\Api\Data\ProductEnquiryInterface;

interface ProductEnquiryRepositoryInterface
{
    /**
     * @param int $enquiryId
     * @return ApiResponseInterface
     */
    public function getById(int $enquiryId): ApiResponseInterface;

    /**
     * @return ApiResponseInterface
     */
    public function getList(): ApiResponseInterface;

    /**
     * @param ProductEnquiryInterface $enquiry
     * @return ApiResponseInterface
     */
    public function save(ProductEnquiryInterface $enquiry): ApiResponseInterface;

    /**
     * @param int $enquiryId
     * @param ProductEnquiryInterface $enquiry
     * @return ApiResponseInterface
     */
    public function update(int $enquiryId, ProductEnquiryInterface $enquiry): ApiResponseInterface;

    /**
     * @param int $enquiryId
     * @return ApiResponseInterface
     */
    public function deleteById(int $enquiryId): ApiResponseInterface;
}
