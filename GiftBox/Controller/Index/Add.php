<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Controller\Index;

use Codilar\GiftBox\Model\Data\GiftBoxAssemblyRequestFactory;
use Codilar\GiftBox\Model\GiftBoxAssemblyValidator;
use Codilar\GiftBox\Model\Pricing\GiftBoxPriceCalculator;
use Codilar\GiftBox\Model\Quote\GiftBoxQuoteManager;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

class Add implements HttpPostActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly JsonFactory $resultJsonFactory,
        private readonly GiftBoxAssemblyRequestFactory $requestFactory,
        private readonly GiftBoxAssemblyValidator $assemblyValidator,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly GiftBoxPriceCalculator $priceCalculator,
        private readonly GiftBoxQuoteManager $quoteManager,
        private readonly UrlInterface $url
    ) {
    }

    public function execute(): Json
    {
        $result = $this->resultJsonFactory->create();

        try {
            $assemblyRequest = $this->buildAssemblyRequest();
            $this->assemblyValidator->validate(
                $assemblyRequest
            );
            $products = $this->getProducts(
                $assemblyRequest->getCoffeeSkus(),
                $assemblyRequest->getEquipmentSku()
            );
            $priceResult = $this->priceCalculator->calculate(
                $products
            );

            $item = $this->quoteManager->addGiftBox(
                $assemblyRequest,
                $priceResult
            );

            return $result->setData([
                'success' => true,
                'message' => __('Gift Box added to cart.'),
                'item_id' => (int) $item->getId(),
                'price' => $priceResult->getComponentSubtotal(),
                'cart_url' => $this->url->getUrl(
                    'checkout/cart'
                ),
            ]);
        } catch (LocalizedException $e) {
            return $result
                ->setHttpResponseCode(400)
                ->setData([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
        } catch (\Throwable $e) {
            return $result
                ->setHttpResponseCode(500)
                ->setData([
                    'success' => false,
                    'message' => __(
                        'Unable to add the Gift Box to cart.'
                    ),
                ]);
        }
    }

    /**
     * @return mixed
     */
    private function buildAssemblyRequest(): mixed
    {
        $coffeeSkus = $this->request->getParam(
            'coffee_skus',
            []
        );

        $equipmentSku = $this->request->getParam(
            'equipment_sku'
        );

        $giftMessage = $this->request->getParam(
            'gift_message'
        );

        if (!is_array($coffeeSkus)) {
            $coffeeSkus = [];
        }

        $coffeeSkus = array_map(
            'strval',
            $coffeeSkus
        );

        $equipmentSku = $equipmentSku !== null
            ? (string) $equipmentSku
            : null;

        $giftMessage = $giftMessage !== null
            ? (string) $giftMessage
            : null;

        return $this->requestFactory
            ->create()
            ->setCoffeeSkus($coffeeSkus)
            ->setEquipmentSku($equipmentSku)
            ->setGiftMessage($giftMessage);
    }

    /**
     * @param array $coffeeSkus
     * @param string|null $equipmentSku
     * @return array
     * @throws NoSuchEntityException
     */
    private function getProducts(
        array $coffeeSkus,
        ?string $equipmentSku
    ): array {
        $products = [];

        foreach ($coffeeSkus as $sku) {
            $products[] = $this->productRepository->get(
                $sku
            );
        }

        if ($equipmentSku !== null && $equipmentSku !== '') {
            $products[] = $this->productRepository->get(
                $equipmentSku
            );
        }

        return $products;
    }
}
