<?php

namespace Codilar\ProductEnquiry\Block\Adminhtml\Enquiry\Edit\Button;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Back implements ButtonProviderInterface
{
    public function __construct(
        private readonly UrlInterface $urlBuilder
    ) {
    }

    public function getButtonData(): array
    {
        return [
            'label' => __('Back'),
            'class' => 'back',
            'on_click' => sprintf(
                "location.href = '%s';",
                $this->urlBuilder->getUrl(
                    'codilar_productenquiry/enquiry/index'
                )
            ),
            'sort_order' => 10,
        ];
    }
}
