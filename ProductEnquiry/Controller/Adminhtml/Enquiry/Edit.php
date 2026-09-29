<?php
namespace Codilar\ProductEnquiry\Controller\Adminhtml\Enquiry;

use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    public const string ADMIN_RESOURCE = 'Codilar_ProductEnquiry::enquiry';

    public function __construct(
        Action\Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly ProductEnquiryRepositoryInterface $productEnquiryRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @return Page|Redirect
     */
    public function execute(): Page|Redirect
    {
        $enquiryId = (int) $this->getRequest()->getParam('enquiry_id');
        if (!$enquiryId) {
            $this->messageManager->addErrorMessage(
                __('Enquiry ID is required.')
            );
            return $this->resultRedirectFactory
                ->create()
                ->setPath('codilar_productenquiry/enquiry/index');
        }
        try {
            $this->productEnquiryRepository->getById($enquiryId);
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(
                __('The requested enquiry does not exist.')
            );
            return $this->resultRedirectFactory
                ->create()
                ->setPath('codilar_productenquiry/enquiry/index');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Magento_Sales::sales_order'
        );
        $resultPage->getConfig()->getTitle()->prepend(
            __('Edit Product Enquiry')
        );
        return $resultPage;
    }
}
