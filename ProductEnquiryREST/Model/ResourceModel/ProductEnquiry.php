<?php

namespace Codilar\ProductEnquiryREST\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProductEnquiry extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('product_enquiry_rest', 'enquiry_id');
    }
}
