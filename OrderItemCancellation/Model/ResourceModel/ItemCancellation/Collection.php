<?php
declare(strict_types=1);

namespace Codilar\OrderItemCancellation\Model\ResourceModel\ItemCancellation;

use Codilar\OrderItemCancellation\Model\ItemCancellation;
use Codilar\OrderItemCancellation\Model\ResourceModel\ItemCancellation as ItemCancellationResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'item_cancellation_id';
    protected function _construct(): void
    {
        $this->_init(
            ItemCancellation::class,
            ItemCancellationResource::class
        );
    }
}
