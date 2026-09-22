<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Model\Quote;

use Codilar\GiftBox\Api\Data\GiftBoxAssemblyRequestInterface;
use Codilar\GiftBox\Api\Data\GiftBoxPriceResultInterface;
use Codilar\GiftBox\Model\GiftBoxProduct;
use Exception;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;

class GiftBoxQuoteManager
{
    public function __construct(
        private readonly CheckoutSession $checkoutSession,
        private readonly GiftBoxProduct $giftBoxProduct,
        private readonly GiftBoxQuoteData $giftBoxQuoteData,
        private readonly GiftBoxInstanceId $giftBoxInstanceId
    ) {
    }

    /**
     * @param GiftBoxAssemblyRequestInterface $request
     * @param GiftBoxPriceResultInterface $priceResult
     * @return Item
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function addGiftBox(
        GiftBoxAssemblyRequestInterface $request,
        GiftBoxPriceResultInterface $priceResult
    ): Item {
        $quote = $this->checkoutSession->getQuote();
        $product = $this->giftBoxProduct->get();
        $instanceId = $this->giftBoxInstanceId->generate();
        $product->addCustomOption(
            'giftbox_instance_id',
            $instanceId
        );
        $item = $quote->addProduct(
            $product,
            1
        );
        if (!$item instanceof Item) {
            throw new LocalizedException(
                __('Unable to add the Gift Box to the cart.')
            );
        }
        $giftBoxData = $this->giftBoxQuoteData->build(
            $instanceId,
            $request,
            $priceResult
        );
        $item->setCustomPrice(
            $priceResult->getComponentSubtotal()
        );
        $item->setOriginalCustomPrice(
            $priceResult->getComponentSubtotal()
        );
        $item->setData(
            'giftbox_data',
            $giftBoxData
        );
        $item->getProduct()->setIsSuperMode(true);
        $quote->collectTotals();
        $quote->save();
        return $item;
    }
}
