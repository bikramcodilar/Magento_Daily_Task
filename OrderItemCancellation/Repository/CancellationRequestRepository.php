<?php
declare(strict_types=1);
namespace Codilar\OrderItemCancellation\Repository;

use Codilar\OrderItemCancellation\Api\CancellationRequestRepositoryInterface;
use Codilar\OrderItemCancellation\Model\CancellationRequest;
use Codilar\OrderItemCancellation\Model\CancellationRequestFactory;
use Codilar\OrderItemCancellation\Model\ResourceModel\CancellationRequest as CancellationRequestResource;
use Codilar\OrderItemCancellation\Model\ResourceModel\CancellationRequest\CollectionFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

class CancellationRequestRepository implements CancellationRequestRepositoryInterface
{
    public function __construct(
        private readonly CancellationRequestResource $resource,
        private readonly CancellationRequestFactory $requestFactory,
        private readonly CollectionFactory $collectionFactory
    ) {
    }
    /**
     * @throws AlreadyExistsException
     */
    public function save(CancellationRequest $request): CancellationRequest
    {
        $this->resource->save($request);
        return $request;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $requestId): CancellationRequest
    {
        $request = $this->requestFactory->create();
        $this->resource->load(
            $request,
            $requestId
        );
        if (!$request->getId()) {
            throw new NoSuchEntityException(
                __('Cancellation request with ID %1 does not exist.', $requestId)
            );
        }
        return $request;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByToken(string $requestToken): CancellationRequest
    {
        $collection = $this->collectionFactory->create();
        $request = $collection->addFieldToFilter('request_token', $requestToken)->setPageSize(1)->getFirstItem();
        if (!$request->getId()) {
            throw new NoSuchEntityException(
                __('Cancellation request does not exist.')
            );
        }
        return $request;
    }
}
