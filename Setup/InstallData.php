<?php
/**
 * @author Atwix Team
 * @copyright Copyright (c) 2018 Atwix (https://www.atwix.com/)
 * @package Atwix_CustomCacheContext
 */
/* File: app/code/Atwix/CustomCacheContext/Setup/InstallData.php */

namespace Atwix\CustomCacheContext\Setup;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Country as CountrySourceModel;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResourceModel;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Default Country Customer Attribute
     */
    const ATTRIBUTE_CUSTOMER_DEFAULT_COUNTRY = 'default_country_id';

    /**
     * Customer Setup Factory
     *
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * Attribute Set Factory
     *
     * @var AttributeSetFactory
     */
    protected $attributeSetFactory;

    /**
     * Attribute Resource Model
     *
     * @var AttributeResourceModel
     */
    protected $attributeResourceModel;

    /**
     * CreateCustomerAttributeService constructor
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param AttributeResourceModel $attributeResourceModel
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        AttributeResourceModel $attributeResourceModel
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeResourceModel = $attributeResourceModel;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Exception
     * @throws LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        /** @var AttributeSet $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            self::ATTRIBUTE_CUSTOMER_DEFAULT_COUNTRY,
            [
                'type' => 'varchar',
                'label' => 'Country',
                'input' => 'select',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'source' => CountrySourceModel::class,
                'default' => '',
                'visible' => true,
                'required' => true,
                'unique' => false,
                'system' => false,
                'user_defined' => true,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
            ]
        );

        $countryAttribute = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            self::ATTRIBUTE_CUSTOMER_DEFAULT_COUNTRY
        );

        $countryAttribute->addData(
            [
                'attribute_set_id'   => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'sort_order' => 100,
                'used_in_forms' => [
                    'adminhtml_customer',
                    'customer_account_create',
                    'customer_account_edit',
                ]
            ]
        );

        $this->attributeResourceModel->save($countryAttribute);
        $setup->endSetup();
    }
}
