<?php

namespace Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry;

use Codilar\ProductEnquiry\Model\ProductEnquiry as ProductEnquiryModel;
use Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry as ProductEnquiryResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            ProductEnquiryModel::class,
            ProductEnquiryResource::class
        );
    }
}
