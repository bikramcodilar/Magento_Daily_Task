<?php
namespace Codilar\ProductEnquiry\Model;

use Codilar\ProductEnquiry\Api\Data\ProductEnquiryInterface;
use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry as ProductEnquiryResource;
use Magento\Framework\Exception\CouldNotSaveException;

class ProductEnquiryRepository implements ProductEnquiryRepositoryInterface
{
    public function __construct(
        private readonly ProductEnquiryResource $productEnquiryResource
    ) {
    }
    /**
     * @throws CouldNotSaveException
     */
    public function save(ProductEnquiryInterface $productEnquiry): ProductEnquiryInterface
    {
        try {
            $this->productEnquiryResource->save($productEnquiry);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Unable to save the product enquiry.'),
                $exception
            );
        }
        return $productEnquiry;
    }
}
