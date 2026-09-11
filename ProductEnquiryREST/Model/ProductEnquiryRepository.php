<?php
namespace Codilar\ProductEnquiryREST\Model;

use Codilar\ProductEnquiryREST\Api\Data\ApiResponseInterface;
use Codilar\ProductEnquiryREST\Api\Data\ProductEnquiryInterface;
use Codilar\ProductEnquiryREST\Api\ProductEnquiryRepositoryInterface;
use Codilar\ProductEnquiryREST\Model\ResourceModel\ProductEnquiry as ProductEnquiryResource;
use Codilar\ProductEnquiryREST\Model\ResourceModel\ProductEnquiry\CollectionFactory;

class ProductEnquiryRepository implements ProductEnquiryRepositoryInterface
{
    public function __construct(
        private readonly ProductEnquiryFactory  $productEnquiryFactory,
        private readonly ProductEnquiryResource $productEnquiryResource,
        private readonly CollectionFactory      $collectionFactory,
        private readonly ApiResponseInterface $response
    ) {
    }

    /**
     * @param $enquiryId
     * @return ApiResponseInterface
     */
    public function getById($enquiryId): ApiResponseInterface
    {
        $enquiry = $this->productEnquiryFactory->create();
        $this->productEnquiryResource->load(
            $enquiry,
            $enquiryId
        );
        if (!$enquiry->getEnquiryId()) {
            return $this->response
                ->setStatus(false)
                ->setMessage(
                    "Product enquiry with ID {$enquiryId} does not exist."
                )
                ->setData([]);
        }
        return $this->response
            ->setStatus(true)
            ->setMessage("Product enquiry fetched successfully.")
            ->setData([
                'enquiry_id' => $enquiry->getEnquiryId(),
                'name'       => $enquiry->getName(),
                'email'      => $enquiry->getEmail(),
                'address'    => $enquiry->getAddress(),
                'quantity'   => $enquiry->getQuantity(),
                'sku'        => $enquiry->getSku()
            ]);
    }

    /**
     * @return ApiResponseInterface
     */
    public function getList(): ApiResponseInterface
    {
        $collection = $this->collectionFactory->create();
        $data = [];
        foreach ($collection->getItems() as $enquiry) {
            $data[] = [
                'enquiry_id' => $enquiry->getEnquiryId(),
                'name'       => $enquiry->getName(),
                'email'      => $enquiry->getEmail(),
                'address'    => $enquiry->getAddress(),
                'quantity'   => $enquiry->getQuantity(),
                'sku'        => $enquiry->getSku()
            ];
        }
        return $this->response
            ->setStatus(true)
            ->setMessage("Product enquiries fetched successfully.")
            ->setData($data);
    }

    /**
     * @param ProductEnquiryInterface $enquiry
     * @return ApiResponseInterface
     */
    public function save(
        ProductEnquiryInterface $enquiry
    ): ApiResponseInterface {
        if ($enquiry->getQuantity() <= 0) {
            return $this->response
                ->setStatus(false)
                ->setMessage("Quantity must be greater than 0.")
                ->setData([]);
        }
        try {
            $this->productEnquiryResource->save($enquiry);
            return $this->response
                ->setStatus(true)
                ->setMessage("Product enquiry created successfully.")
                ->setData([
                    'enquiry_id' => $enquiry->getEnquiryId(),
                    'name'       => $enquiry->getName(),
                    'email'      => $enquiry->getEmail(),
                    'address'    => $enquiry->getAddress(),
                    'quantity'   => $enquiry->getQuantity(),
                    'sku'        => $enquiry->getSku()
                ]);

        } catch (\Exception $e) {
            return $this->response
                ->setStatus(false)
                ->setMessage("Could not create product enquiry.")
                ->setData([]);
        }
    }

    /**
     * @param $enquiryId
     * @param ProductEnquiryInterface $enquiry
     * @return ApiResponseInterface
     */
    public function update(
        $enquiryId,
        ProductEnquiryInterface $enquiry
    ): ApiResponseInterface {
        if ($enquiry->getQuantity() <= 0) {
            return $this->response
                ->setStatus(false)
                ->setMessage("Quantity must be greater than 0.")
                ->setData([]);
        }
        $existingEnquiry = $this->productEnquiryFactory->create();
        $this->productEnquiryResource->load(
            $existingEnquiry,
            $enquiryId
        );
        if (!$existingEnquiry->getEnquiryId()) {
            return $this->response
                ->setStatus(false)
                ->setMessage(
                    "Product enquiry with ID {$enquiryId} does not exist."
                )
                ->setData([]);
        }
        $existingEnquiry->setName($enquiry->getName());
        $existingEnquiry->setEmail($enquiry->getEmail());
        $existingEnquiry->setAddress($enquiry->getAddress());
        $existingEnquiry->setQuantity($enquiry->getQuantity());
        $existingEnquiry->setSku($enquiry->getSku());
        try {
            $this->productEnquiryResource->save($existingEnquiry);
            return $this->response
                ->setStatus(true)
                ->setMessage("Product enquiry updated successfully.")
                ->setData([
                    'enquiry_id' => $existingEnquiry->getEnquiryId(),
                    'name'       => $existingEnquiry->getName(),
                    'email'      => $existingEnquiry->getEmail(),
                    'address'    => $existingEnquiry->getAddress(),
                    'quantity'   => $existingEnquiry->getQuantity(),
                    'sku'        => $existingEnquiry->getSku()
                ]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatus(false)
                ->setMessage("Could not update product enquiry.")
                ->setData([]);
        }
    }

    /**
     * @param $enquiryId
     * @return ApiResponseInterface
     */
    public function deleteById($enquiryId): ApiResponseInterface
    {
        $enquiry = $this->productEnquiryFactory->create();
        $this->productEnquiryResource->load(
            $enquiry,
            $enquiryId
        );
        if (!$enquiry->getEnquiryId()) {
            return $this->response
                ->setStatus(false)
                ->setMessage(
                    "Product enquiry with ID {$enquiryId} does not exist."
                )
                ->setData([]);
        }
        try {
            $this->productEnquiryResource->delete($enquiry);
            return $this->response
                ->setStatus(true)
                ->setMessage("Product enquiry deleted successfully.")
                ->setData([]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatus(false)
                ->setMessage("Could not delete product enquiry.")
                ->setData([]);
        }
    }
}
