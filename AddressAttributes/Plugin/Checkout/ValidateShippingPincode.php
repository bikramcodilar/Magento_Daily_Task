<?php

namespace Codilar\AddressAttributes\Plugin\Checkout;

use Codilar\AddressAttributes\Model\PincodeValidator;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;

class ValidateShippingPincode
{
    public function __construct(
        private readonly PincodeValidator $pincodeValidator
    ) {
    }

    /**
     * @param ShippingInformationManagementInterface $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     * @throws LocalizedException
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagementInterface $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ): array {
        if (!$this->pincodeValidator->isEnabled()) {
            return [$cartId, $addressInformation];
        }
        $address = $addressInformation->getShippingAddress();
        $postcode = $address instanceof AddressInterface ? (string)$address->getPostcode() : '';
        if (!$this->pincodeValidator->isAllowed($postcode)) {
            throw new LocalizedException(
                __('We do not deliver to this pincode. Please enter a different shipping address.')
            );
        }
        return [$cartId, $addressInformation];
    }
}
