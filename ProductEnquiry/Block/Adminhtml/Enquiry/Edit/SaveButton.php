<?php

namespace Codilar\ProductEnquiry\Block\Adminhtml\Enquiry\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'save',
                        'target' => '#edit_form',
                    ],
                ],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
