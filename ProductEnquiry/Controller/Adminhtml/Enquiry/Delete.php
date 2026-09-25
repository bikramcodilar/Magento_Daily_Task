<?php

namespace Codilar\ProductEnquiry\Controller\Adminhtml\Enquiry;

use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;

class Delete extends Action
{
    public const string ADMIN_RESOURCE = 'Codilar_ProductEnquiry::enquiry';

    public function __construct(
        Context $context,
        private readonly ProductEnquiryRepositoryInterface $productEnquiryRepository
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $enquiryId = (int) $this->getRequest()->getParam('enquiry_id');

        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$enquiryId) {
            $this->messageManager->addErrorMessage(
                __('We cannot find the enquiry.')
            );

            return $resultRedirect->setPath('*/*/index');
        }

        try {
            $deleted = $this->productEnquiryRepository->deleteById($enquiryId);

            if ($deleted) {
                $this->messageManager->addSuccessMessage(
                    __('The enquiry has been deleted.')
                );
            } else {
                $this->messageManager->addErrorMessage(
                    __('The enquiry does not exist.')
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while deleting the enquiry.')
            );
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
