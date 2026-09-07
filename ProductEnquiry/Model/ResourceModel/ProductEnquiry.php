<?php

namespace Codilar\ProductEnquiry\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProductEnquiry extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('product_enquiry', 'enquiry_id');
    }
}
