<?php

namespace Codilar\ProductEnquiry\Controller\Adminhtml\Enquiry;

use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validator\EmailAddress;
use Codilar\ProductEnquiry\Model\ProductEnquiryFactory;

class Save extends Action
{
    public const string ADMIN_RESOURCE = 'Codilar_ProductEnquiry::enquiry';

    public function __construct(
        Action\Context $context,
        private readonly ProductEnquiryRepositoryInterface $productEnquiryRepository,
        private readonly EmailAddress $emailValidator,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductEnquiryFactory $productEnquiryFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $data = $this->getRequest()->getPostValue();
        $enquiryId = (int)($data['enquiry_id'] ?? 0);
        $email = trim((string)($data['email'] ?? ''));
        if (!$this->emailValidator->isValid($email)) {
            $this->messageManager->addErrorMessage(
                __('Please enter a valid email address.')
            );
            return $this->resultRedirectFactory
                ->create()
                ->setPath(
                    'codilar_productenquiry/enquiry/edit',
                    ['enquiry_id' => $enquiryId]
                );
        }
        $quantity = $data['quantity'] ?? null;
        if (!is_numeric($quantity) || (float)$quantity <= 0) {
            $this->messageManager->addErrorMessage(
                __('Quantity must be a valid number greater than 0.')
            );
            return $this->resultRedirectFactory
                ->create()
                ->setPath(
                    'codilar_productenquiry/enquiry/edit',
                    ['enquiry_id' => $enquiryId]
                );
        }
        $sku = trim((string)($data['sku'] ?? ''));
        try {
            $this->productRepository->get($sku);
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(
                __('The product with SKU "%1" does not exist.', $sku)
            );
            return $this->resultRedirectFactory
                ->create()
                ->setPath(
                    'codilar_productenquiry/enquiry/edit',
                    ['enquiry_id' => $enquiryId]
                );
        }
        try {
            if ($enquiryId) {
                $productEnquiry = $this->productEnquiryRepository->getById($enquiryId);
            } else {
                $productEnquiry = $this->productEnquiryFactory->create();
            }
            $productEnquiry->setName(
                $data['name'] ?? ''
            );
            $productEnquiry->setEmail(
                $email
            );
            $productEnquiry->setSku(
                $sku
            );
            $productEnquiry->setQuantity(
                (float)$quantity
            );
            $this->productEnquiryRepository->save(
                $productEnquiry
            );
            $enquiryId = $productEnquiry->getEnquiryId();
            $this->messageManager->addSuccessMessage(
                __('Product enquiry has been saved.')
            );
            return $this->resultRedirectFactory->create()->setPath(
                'codilar_productenquiry/enquiry/edit',
                ['enquiry_id' => $enquiryId]
            );
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(
                __('Unable to save the product enquiry.')
            );
            return $this->resultRedirectFactory
                ->create()
                ->setPath(
                    'codilar_productenquiry/enquiry/edit',
                    ['enquiry_id' => $enquiryId]
                );
        }
    }
}
