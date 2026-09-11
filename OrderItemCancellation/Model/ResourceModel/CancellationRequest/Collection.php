<?php
declare(strict_types=1);
namespace Codilar\OrderItemCancellation\Model\ResourceModel\CancellationRequest;

use Codilar\OrderItemCancellation\Model\CancellationRequest;
use Codilar\OrderItemCancellation\Model\ResourceModel\CancellationRequest as CancellationRequestResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'request_id';
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            CancellationRequest::class,
            CancellationRequestResource::class
        );
    }
}
