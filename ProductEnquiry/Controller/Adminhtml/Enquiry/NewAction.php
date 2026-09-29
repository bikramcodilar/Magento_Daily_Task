<?php

namespace Codilar\ProductEnquiry\Controller\Adminhtml\Enquiry;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class NewAction extends Action
{
    public const string ADMIN_RESOURCE = 'Codilar_ProductEnquiry::enquiry';

    public function __construct(
        Action\Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu(
            'Magento_Sales::sales_order'
        );

        $resultPage->getConfig()->getTitle()->prepend(
            __('New Product Enquiry')
        );

        return $resultPage;
    }
}
