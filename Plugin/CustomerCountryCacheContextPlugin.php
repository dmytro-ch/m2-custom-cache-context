<?php
/**
 * @author Atwix Team
 * @copyright Copyright (c) 2018 Atwix (https://www.atwix.com/)
 * @package Atwix_CustomCacheContext
 */
/* File: app/code/Atwix/CustomCacheContext/Plugin/CustomerCountryCacheContextPlugin.php */

namespace Atwix\CustomCacheContext\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Class CustomerCountryCacheContextPlugin
 */
class CustomerCountryCacheContextPlugin
{
    /**
     * Default Country Customer Attribute
     */
    const ATTRIBUTE_CUSTOMER_DEFAULT_COUNTRY = 'default_country_id';

    /**
     * Customer group cache context
     */
    const COUNTRY_CONTEXT_GROUP = 'customer_county';

    /**
     * Customer group cache context
     */
    const NOT_LOGGED_IN_CUSTOMER_COUNTRY_VALUE = '';

    /**
     * Customer Session
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * CustomerCountryCacheContextPlugin constructor
     *
     * @param Session $customerSession
     */
    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * Add Customer Country cache context for caching purposes
     *
     * @param HttpContext $subject
     *
     * @return array
     */
    public function beforeGetVaryString(HttpContext $subject)
    {
        $customerData = $this->customerSession->getCustomerData();

        if (!($customerData instanceof CustomerInterface)) {
            $subject->setValue(
                self::COUNTRY_CONTEXT_GROUP,
                self::NOT_LOGGED_IN_CUSTOMER_COUNTRY_VALUE,
                self::NOT_LOGGED_IN_CUSTOMER_COUNTRY_VALUE
            );

            return [];
        }

        $countryCodeAttribute = $customerData->getCustomAttribute(
            self::ATTRIBUTE_CUSTOMER_DEFAULT_COUNTRY
        );

        if (!($countryCodeAttribute instanceof AttributeInterface)) {
            $subject->setValue(
                self::COUNTRY_CONTEXT_GROUP,
                self::NOT_LOGGED_IN_CUSTOMER_COUNTRY_VALUE,
                self::NOT_LOGGED_IN_CUSTOMER_COUNTRY_VALUE
            );

            return [];
        }

        $countryCode = $countryCodeAttribute->getValue();
        $subject->setValue(self::COUNTRY_CONTEXT_GROUP, $countryCode, self::NOT_LOGGED_IN_CUSTOMER_COUNTRY_VALUE);

        return [];
    }
}
