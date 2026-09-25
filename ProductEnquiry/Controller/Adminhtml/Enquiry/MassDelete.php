<?php

namespace Codilar\ProductEnquiry\Controller\Adminhtml\Enquiry;

use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;

class MassDelete extends Action
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
        $selectedIds = $this->getRequest()->getParam('selected', []);
        $resultRedirect = $this->resultRedirectFactory->create();
        if (empty($selectedIds)) {
            $this->messageManager->addErrorMessage(
                __('Please select at least one enquiry.')
            );
            return $resultRedirect->setPath('*/*/index');
        }
        try {
            $deleted = $this->productEnquiryRepository->deleteByIds(
                array_map('intval', $selectedIds)
            );
            if ($deleted) {
                $this->messageManager->addSuccessMessage(
                    __('The selected enquiries have been deleted.')
                );
            } else {
                $this->messageManager->addErrorMessage(
                    __('No enquiries were deleted.')
                );
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while deleting the enquiries.')
            );
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
