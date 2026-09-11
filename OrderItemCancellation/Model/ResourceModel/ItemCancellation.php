<?php
declare(strict_types=1);

namespace Codilar\OrderItemCancellation\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ItemCancellation extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            'order_item_cancellation',
            'item_cancellation_id'
        );
    }
}
