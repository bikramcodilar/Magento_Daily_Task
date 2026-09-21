<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class GiftBoxProduct
{
    private const string SKU = 'gift-box';

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function get(): ProductInterface
    {
        return $this->productRepository->get(
            self::SKU,
            false,
            $this->storeManager->getStore()->getId()
        );
    }
}
