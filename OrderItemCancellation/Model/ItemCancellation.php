<?php
declare(strict_types=1);

namespace Codilar\OrderItemCancellation\Model;

use Codilar\OrderItemCancellation\Model\ResourceModel\ItemCancellation as ItemCancellationResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class ItemCancellation extends AbstractModel
{
    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(
            ItemCancellationResource::class
        );
    }
}
