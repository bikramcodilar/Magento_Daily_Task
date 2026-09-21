<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Model\Quote;

use Magento\Framework\Serialize\Serializer\Json;

class GiftBoxQuoteDataReader
{
    public function __construct(
        private readonly Json $json
    ) {
    }

    /**
     * @param object $item
     * @return array
     */
    public function get(object $item): array
    {
        return $this->getFromValue(
            $item->getData('giftbox_data')
        );
    }

    /**
     * @param string|null $giftBoxData
     * @return array
     */
    public function getFromValue(?string $giftBoxData): array
    {
        if (!$giftBoxData) {
            return [];
        }
        return $this->json->unserialize($giftBoxData);
    }
}
