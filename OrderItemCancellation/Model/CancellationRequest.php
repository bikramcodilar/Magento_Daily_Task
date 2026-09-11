<?php
declare(strict_types=1);

namespace Codilar\OrderItemCancellation\Model;

use Codilar\OrderItemCancellation\Model\ResourceModel\CancellationRequest as CancellationRequestResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class CancellationRequest extends AbstractModel
{
    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(
            CancellationRequestResource::class
        );
    }
}
