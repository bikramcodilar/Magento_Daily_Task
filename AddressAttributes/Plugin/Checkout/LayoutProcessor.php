<?php

namespace Codilar\AddressAttributes\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor as Subject;

class LayoutProcessor
{
    public function afterProcess(
        Subject $subject,
        array $jsLayout
    ): array {
        $fields = &$jsLayout['components']['checkout']['children']['steps']
        ['children']['shipping-step']['children']['shippingAddress']['children']
        ['shipping-address-fieldset']['children'];

        $fields['building_name'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'dataScope' => 'shippingAddress.custom_attributes.building_name',
            'label' => __('Building Name'),
            'provider' => 'checkoutProvider',
            'sortOrder' => 75,
            'visible' => true,
            'validation' => [
                'required-entry' => false,
            ],
        ];

        $fields['unit_number'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'dataScope' => 'shippingAddress.custom_attributes.unit_number',
            'label' => __('Unit Number'),
            'provider' => 'checkoutProvider',
            'sortOrder' => 76,
            'visible' => true,
            'validation' => [
                'required-entry' => false,
            ],
        ];
        return $jsLayout;
    }
}
