<?php

namespace Codilar\ProductEnquiry\Controller\Index;

use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Codilar\ProductEnquiry\Logger\Logger;
use Codilar\ProductEnquiry\Model\ProductEnquiryFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;

class Submit extends Action
{
    public function __construct(
        Context $context,
        private readonly JsonFactory $resultJsonFactory,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductEnquiryFactory $productEnquiryFactory,
        private readonly ProductEnquiryRepositoryInterface $productEnquiryRepository,
        private readonly Logger $logger
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->getRequest()->isPost()) {
            return $result->setData([
                'success' => false,
                'message' => __('Invalid request.')
            ]);
        }

        try {
            $name = trim((string) $this->getRequest()->getParam('name'));
            $email = trim((string) $this->getRequest()->getParam('email'));
            $quantity = (float) $this->getRequest()->getParam('quantity');
            $productId = (int) $this->getRequest()->getParam('product_id');

            if (!$name || !$email || !$productId || $quantity <= 0) {
                throw new LocalizedException(
                    __('Please fill all required fields correctly.')
                );
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new LocalizedException(
                    __('Please enter a valid email address.')
                );
            }

            // Load SKU from Magento; do not trust an SKU sent by the browser.
            $product = $this->productRepository->getById($productId);

            if ($product->getTypeId() !== Type::TYPE_SIMPLE) {
                throw new LocalizedException(
                    __('Product enquiry is available only for simple products.')
                );
            }

            $productEnquiry = $this->productEnquiryFactory->create();

            $productEnquiry->setName($name);
            $productEnquiry->setEmail($email);
            $productEnquiry->setSku($product->getSku());
            $productEnquiry->setQuantity($quantity);

            $this->productEnquiryRepository->save($productEnquiry);

            $this->logger->info('Product enquiry submitted', [
                'enquiry_id' => $productEnquiry->getEnquiryId(),
                'name' => $productEnquiry->getName(),
                'email' => $productEnquiry->getEmail(),
                'sku' => $productEnquiry->getSku(),
                'quantity' => $productEnquiry->getQuantity()
            ]);

            return $result->setData([
                'success' => true,
                'message' => __('Thank you. Your enquiry has been submitted.')
            ]);
        } catch (LocalizedException $exception) {
            return $result->setData([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        } catch (\Exception $exception) {
            $this->logger->error('Product enquiry submission failed', [
                'exception' => $exception->getMessage()
            ]);

            return $result->setData([
                'success' => false,
                'message' => __('Unable to submit your enquiry. Please try again.')
            ]);
        }
    }
}
