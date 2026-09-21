<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model\Quote;
use Magento\Framework\Math\Random;
use Magento\Framework\Exception\LocalizedException;
class GiftBoxInstanceId
{
    public function __construct(
        private readonly Random $random
    ) {
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function generate(): string
    {
        return $this->random->getUniqueHash();
    }
}
