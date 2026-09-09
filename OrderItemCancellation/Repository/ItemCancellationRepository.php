<?php
declare(strict_types=1);
namespace Codilar\OrderItemCancellation\Repository;

use Codilar\OrderItemCancellation\Api\ItemCancellationRepositoryInterface;
use Codilar\OrderItemCancellation\Model\ItemCancellation;
use Codilar\OrderItemCancellation\Model\ResourceModel\ItemCancellation as ItemCancellationResource;
use Magento\Framework\Exception\AlreadyExistsException;

class ItemCancellationRepository implements ItemCancellationRepositoryInterface
{
    public function __construct(
        private readonly ItemCancellationResource $resource
    ) {
    }
    /**
     * @throws AlreadyExistsException
     */
    public function save(ItemCancellation $itemCancellation): ItemCancellation
    {
        $this->resource->save($itemCancellation);
        return $itemCancellation;
    }
}
