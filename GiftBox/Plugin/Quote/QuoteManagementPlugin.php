<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Plugin\Quote;

use Codilar\GiftBox\Model\Checkout\GiftBoxCheckoutValidator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;

class QuoteManagementPlugin
{
    public function __construct(
        private readonly GiftBoxCheckoutValidator $giftBoxCheckoutValidator
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function beforeSubmit(
        QuoteManagement $subject,
        Quote $quote
    ): array {
        $this->giftBoxCheckoutValidator->validateQuote($quote);
        return [$quote];
    }
}
