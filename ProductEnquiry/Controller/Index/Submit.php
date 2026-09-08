<?php
namespace Codilar\ProductEnquiry\Controller\Index;

use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Codilar\ProductEnquiry\Logger\Logger;
use Codilar\ProductEnquiry\Model\ProductEnquiryFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;

class Submit implements ActionInterface
{
    /**
     * @param JsonFactory $resultJsonFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductEnquiryFactory $productEnquiryFactory
     * @param ProductEnquiryRepositoryInterface $productEnquiryRepository
     * @param Logger $logger
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly JsonFactory $resultJsonFactory,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductEnquiryFactory $productEnquiryFactory,
        private readonly ProductEnquiryRepositoryInterface $productEnquiryRepository,
        private readonly Logger $logger,
        private readonly RequestInterface $request
    ) {
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultJsonFactory->create();
        if (!$this->request->isPost()) {
            return $result->setData([
                'success' => false,
                'message' => __('Invalid request.')
            ]);
        }
        try {
            $name = trim((string) $this->request->getParam('name'));
            $email = trim((string) $this->request->getParam('email'));
            $quantity = (float) $this->request->getParam('quantity');
            $productId = (int) $this->request->getParam('product_id');
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
