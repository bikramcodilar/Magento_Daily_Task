<?php

namespace Codilar\ProductEnquiryREST\Model\ResourceModel\ProductEnquiry;

use Codilar\ProductEnquiryREST\Model\ProductEnquiry;
use Codilar\ProductEnquiryREST\Model\ResourceModel\ProductEnquiry as ProductEnquiryResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'enquiry_id';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ProductEnquiry::class, ProductEnquiryResource::class);
    }
}
