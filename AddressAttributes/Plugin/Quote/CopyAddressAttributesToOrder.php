<?php

namespace Codilar\AddressAttributes\Plugin\Quote;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\ToOrderAddress;
use Magento\Sales\Api\Data\OrderAddressInterface;

class CopyAddressAttributesToOrder
{
    public function afterConvert(
        ToOrderAddress $subject,
        OrderAddressInterface $result,
        Address $source,
        $data = []
    ): OrderAddressInterface {
        foreach (['building_name', 'unit_number'] as $field) {
            $result->setData($field, $source->getData($field));
        }
        return $result;
    }
}
