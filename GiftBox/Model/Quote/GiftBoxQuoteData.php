<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model\Quote;

use Codilar\GiftBox\Api\Data\GiftBoxAssemblyRequestInterface;
use Codilar\GiftBox\Api\Data\GiftBoxPriceResultInterface;
use Magento\Framework\Serialize\Serializer\Json;

class GiftBoxQuoteData
{
    public function __construct(
        private readonly Json $json
    ) {
    }

    /**
     * @param string $instanceId
     * @param GiftBoxAssemblyRequestInterface $request
     * @param GiftBoxPriceResultInterface $priceResult
     * @return string
     */
    public function build(
        string $instanceId,
        GiftBoxAssemblyRequestInterface $request,
        GiftBoxPriceResultInterface $priceResult
    ): string {
        $data = [
            'instance_id' => $instanceId,
            'coffee_skus' => $request->getCoffeeSkus(),
            'equipment_sku' => $request->getEquipmentSku(),
            'gift_message' => $request->getGiftMessage(),
            'component_prices' => $priceResult->getComponentPrices(),
            'component_subtotal' => $priceResult->getComponentSubtotal()
        ];
        return $this->json->serialize($data);
    }
}
