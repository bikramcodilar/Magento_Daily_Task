<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Controller\Test;

use Codilar\GiftBox\Model\Data\GiftBoxAssemblyRequest;
use Codilar\GiftBox\Model\GiftBoxAssemblyValidator;
use Codilar\GiftBox\Model\Pricing\GiftBoxPriceCalculator;
use Codilar\GiftBox\Model\Quote\GiftBoxQuoteManager;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class Add implements ActionInterface
{
    public function __construct(
        private readonly JsonFactory $resultJsonFactory,
        private readonly GiftBoxAssemblyValidator $assemblyValidator,
        private readonly GiftBoxPriceCalculator $priceCalculator,
        private readonly GiftBoxQuoteManager $quoteManager,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @return Json
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(): Json
    {

        $request = new GiftBoxAssemblyRequest();
        $request->setCoffeeSkus([
            'coffee-d',
            'coffee-e',
            'coffee-f',
        ]);

        $request->setEquipmentSku(
            'equipment-a'
        );

        $request->setGiftMessage(
            'Congo!'
        );
        $this->assemblyValidator->validate(
            $request
        );
        $storeId = $this->storeManager
            ->getStore()
            ->getId();
        $products = [];
        foreach ($request->getCoffeeSkus() as $sku) {
            $products[] = $this->productRepository->get(
                $sku,
                false,
                $storeId
            );
        }

        if ($request->getEquipmentSku()) {
            $products[] = $this->productRepository->get(
                $request->getEquipmentSku(),
                false,
                $storeId
            );
        }
        $priceResult = $this->priceCalculator->calculate(
            $products
        );
        $item = $this->quoteManager->addGiftBox(
            $request,
            $priceResult
        );
        $result = $this->resultJsonFactory->create();
        return $result->setData([
            'message' => 'Gift Box added successfully.',
            'item_id' => $item->getItemId(),
            'quote_id' => $item->getQuoteId(),
            'product_id' => $item->getProductId(),
            'sku' => $item->getSku(),
            'qty' => $item->getQty(),
            'price' => $item->getPrice(),
            'custom_price' => $item->getCustomPrice(),
            'original_custom_price' => $item->getOriginalCustomPrice(),
            'row_total' => $item->getRowTotal(),
            'giftbox_data' => $item->getData('giftbox_data'),
        ]);
    }
}
