<?php

namespace Codilar\AddressAttributes\Setup\Patch\Data;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AssignAddressAttributesToSet implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CustomerSetupFactory $customerSetupFactory
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create([
            'setup' => $this->moduleDataSetup
        ]);
        $entityTypeId = $customerSetup->getEntityTypeId(
            'customer_address'
        );
        $attributeSetId = $customerSetup->getDefaultAttributeSetId(
            $entityTypeId
        );
        $attributeGroupId = $customerSetup->getDefaultAttributeGroupId(
            $entityTypeId,
            $attributeSetId
        );
        $customerSetup->addAttributeToSet(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            'building_name',
            100
        );
        $customerSetup->addAttributeToSet(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            'unit_number',
            110
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies(): array
    {
        return [
            AddAddressAttributes::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }
}
