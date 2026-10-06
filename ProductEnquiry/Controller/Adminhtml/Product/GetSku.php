<?php

namespace Codilar\ProductEnquiry\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class GetSku extends Action
{
    public const string ADMIN_RESOURCE = 'Codilar_ProductEnquiry::enquiry';

    public function __construct(
        Action\Context $context,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Json
    {
        $productId = (int) $this->getRequest()->getParam('product_id');
        if (!$productId) {
            return $this->resultJsonFactory->create()->setData([
                'success' => false,
                'message' => 'Product ID is required.'
            ]);
        }
        try {
            $product = $this->productRepository->getById($productId);
            return $this->resultJsonFactory->create()->setData([
                'success' => true,
                'sku' => $product->getSku()
            ]);
        } catch (\Exception $exception) {
            return $this->resultJsonFactory->create()->setData([
                'success' => false,
                'message' => 'Unable to load the product.'
            ]);
        }
    }
}
