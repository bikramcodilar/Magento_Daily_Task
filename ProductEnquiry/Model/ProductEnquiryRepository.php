<?php

namespace Codilar\ProductEnquiry\Model;

use Codilar\ProductEnquiry\Api\Data\ProductEnquiryInterface;
use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry as ProductEnquiryResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class ProductEnquiryRepository implements ProductEnquiryRepositoryInterface
{
    public function __construct(
        private readonly ProductEnquiryResource $productEnquiryResource,
        private readonly ProductEnquiryFactory $productEnquiryFactory
    ) {
    }

    /**
     * @param ProductEnquiryInterface $productEnquiry
     * @return ProductEnquiryInterface
     * @throws CouldNotSaveException
     */
    public function save(
        ProductEnquiryInterface $productEnquiry
    ): ProductEnquiryInterface {
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

    /**
     * @param int $enquiryId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $enquiryId): bool
    {
        try {
            $productEnquiry = $this->productEnquiryFactory->create();
            $this->productEnquiryResource->load(
                $productEnquiry,
                $enquiryId
            );
            if (!$productEnquiry->getId()) {
                return false;
            }
            $this->productEnquiryResource->delete($productEnquiry);
            return true;
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Unable to delete the product enquiry.'),
                $exception
            );
        }
    }

    /**
     * @param array $enquiryIds
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteByIds(array $enquiryIds): bool
    {
        if (empty($enquiryIds)) {
            return false;
        }

        try {
            $connection = $this->productEnquiryResource->getConnection();
            $connection->delete(
                $this->productEnquiryResource->getMainTable(),
                [
                    'enquiry_id IN (?)' => $enquiryIds
                ]
            );
            return true;
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Unable to delete the product enquiries.'),
                $exception
            );
        }
    }
}
