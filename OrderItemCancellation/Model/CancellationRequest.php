<?php
declare(strict_types=1);

namespace Codilar\OrderItemCancellation\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use \Codilar\OrderItemCancellation\Model\ResourceModel\CancellationRequest as CancellationRequestResource;
class CancellationRequest extends AbstractModel
{
    /**
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(
            CancellationRequestResource::class
        );
    }
}
