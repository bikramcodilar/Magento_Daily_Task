<?php

namespace Codilar\AddressAttributes\Model\Quote\Address;

use Magento\Quote\Model\Quote\Address\CustomAttributeListInterface;

class CustomAttributeList implements CustomAttributeListInterface
{
    public function getAttributes(): array
    {
        return [
            'building_name' => [],
            'unit_number' => [],
        ];
    }
}
