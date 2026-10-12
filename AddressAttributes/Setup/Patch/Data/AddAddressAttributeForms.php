<?php

namespace Codilar\AddressAttributes\Setup\Patch\Data;

use Exception;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddAddressAttributeForms implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CustomerSetupFactory $customerSetupFactory
    ) {
    }

    /**
     * @throws LocalizedException
     * @throws Exception
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create([
            'setup' => $this->moduleDataSetup
        ]);
        $formCodes = [
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address',
        ];

        foreach (['building_name', 'unit_number'] as $attributeCode) {
            $attribute = $customerSetup->getEavConfig()->getAttribute(
                'customer_address',
                $attributeCode
            );
            $attribute->setData('used_in_forms', $formCodes);
            $attribute->save();
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
