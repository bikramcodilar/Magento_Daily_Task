<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model;
use Codilar\GiftBox\Api\Data\GiftBoxAssemblyRequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GiftBoxAssemblyValidator
{
    public function __construct(
        private readonly Config $config,
        private readonly GiftBoxProductValidator $productValidator
    ) {
    }
    /**
     * @param GiftBoxAssemblyRequestInterface $request
     * @return void
     * @throws LocalizedException
     */
    public function validateCoffeeCount(
        GiftBoxAssemblyRequestInterface $request
    ): void {
        $requiredCount = $this->config->getRequiredCoffeeCount();
        $selectedCoffees = $request->getCoffeeSkus();
        $selectedCount = count($selectedCoffees);
        if ($selectedCount !== $requiredCount) {
            throw new LocalizedException(
                __(
                    'Please select exactly %1 coffee products.',
                    $requiredCount
                )
            );
        }
    }

    /**
     * @param GiftBoxAssemblyRequestInterface $request
     * @return void
     * @throws LocalizedException
     */
    public function validateUniqueCoffeeSelections(
        GiftBoxAssemblyRequestInterface $request
    ): void {
        $coffeeSkus = $request->getCoffeeSkus();
        $uniqueCoffeeSkus = array_unique($coffeeSkus);
        if (count($coffeeSkus) !== count($uniqueCoffeeSkus)) {
            throw new LocalizedException(
                __('You cannot select the same coffee product more than once.')
            );
        }
    }

    /**
     * @param GiftBoxAssemblyRequestInterface $request
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validateEquipment(
        GiftBoxAssemblyRequestInterface $request
    ): void {
        $equipmentSku = $request->getEquipmentSku();
        if ($equipmentSku === null || $equipmentSku === '') {
            return;
        }
        $equipmentCategoryId = $this->config->getEquipmentCategoryId();
        if ($equipmentCategoryId <= 0) {
            throw new LocalizedException(
                __('Equipment category is not configured.')
            );
        }
        $this->productValidator->validate(
            $equipmentSku,
            $equipmentCategoryId
        );
    }

    /**
     * @param GiftBoxAssemblyRequestInterface $request
     * @return void
     * @throws LocalizedException
     */
    public function validateGiftMessage(
        GiftBoxAssemblyRequestInterface $request
    ): void {
        $giftMessage = $request->getGiftMessage();
        if ($giftMessage === null || trim($giftMessage) === '') {
            return;
        }
        $maxLength = $this->config->getGiftMessageMaxLength();
        if ($maxLength <= 0) {
            throw new LocalizedException(
                __('Gift message configuration is invalid.')
            );
        }
        $messageLength = mb_strlen(
            $giftMessage,
            'UTF-8'
        );
        if ($messageLength > $maxLength) {
            throw new LocalizedException(
                __(
                    'Gift message cannot exceed %1 characters.',
                    $maxLength
                )
            );
        }
    }

    /**
     * @param GiftBoxAssemblyRequestInterface $request
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validateCoffeeProducts(
        GiftBoxAssemblyRequestInterface $request
    ): void {
        $coffeeCategoryId = $this->config->getCoffeeCategoryId();
        if ($coffeeCategoryId <= 0) {
            throw new LocalizedException(
                __('Coffee category is not configured.')
            );
        }
        foreach ($request->getCoffeeSkus() as $sku) {
            $this->productValidator->validate(
                $sku,
                $coffeeCategoryId
            );
        }
    }

    /**
     * @param GiftBoxAssemblyRequestInterface $request
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validate(
        GiftBoxAssemblyRequestInterface $request
    ): void {
        $this->validateCoffeeCount($request);
        $this->validateUniqueCoffeeSelections($request);
        $this->validateCoffeeProducts($request);
        $this->validateEquipment($request);
        $this->validateGiftMessage($request);
    }
}
