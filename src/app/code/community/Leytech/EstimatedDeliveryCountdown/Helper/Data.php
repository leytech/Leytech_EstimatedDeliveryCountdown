<?php
/**
 * @package    Leytech_EstimatedDeliveryCountdown
 * @author     Chris Nolan (chris@leytech.co.uk)
 * @copyright  Copyright (c) 2018 Chris Nolan
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Leytech_EstimatedDeliveryCountdown_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'meanbee_estimateddelivery/leytech_countdown/enabled';
    const XML_PATH_DELIVERY_METHOD = 'meanbee_estimateddelivery/leytech_countdown/delivery_method';
    const XML_PATH_SHOW_SECONDS = 'meanbee_estimateddelivery/leytech_countdown/show_seconds';

    /**
     * Get the delivery method to use for the countdown
     *
     * @return mixed
     */
    protected function getDeliveryMethod() {
        return Mage::getStoreConfig(self::XML_PATH_DELIVERY_METHOD);
    }

    /**
     * Get whether the countdown is enabled
     *
     * @return bool
     */
    protected function isEnabled() {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Get the estimated delivery rule to use
     *
     * @return mixed
     */
    protected function getEstimatedDeliveryRule() {
        return Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->loadByShippingMethod($this->getDeliveryMethod());
    }

    /**
     * Get whether the countdown should show seconds
     *
     * @return bool
     */
    public function getShowSeconds() {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_SECONDS);
    }

    /**
     * Determine whether the countdown can be shown
     *
     * @return bool
     */
    public function canShowCountdown() {
        if (!$this->isEnabled()) {
            // Module is disabled
            return false;
        }
        $rule = $this->getEstimatedDeliveryRule();
        if(!$rule->getId()) {
            // Rule doesn't exist for the configured delivery method
            return false;
        }
        return true;
    }

    /**
     * Get the estimated delivery date
     *
     * @param string $dateFormat
     * @return mixed
     */
    public function getEstimatedDeliveryDate($dateFormat = 'YYYY-MM-dd') {
        $helper = Mage::helper('meanbee_estimateddelivery');
        return $helper->getEstimatedDeliveryFromString($this->getDeliveryMethod(), null, $dateFormat);
    }

    /**
     * Return the next dispatch cut-off date/time in a string formatted 'YYYY/MM/DD hh:mm:ss'
     *
     * @return string
     */
    public function getNextCutOff() {
        $helper = Mage::helper('meanbee_estimateddelivery');
        $date = $helper->getDispatchDateString($this->getDeliveryMethod(), null, 'YYYY/MM/dd');

        $rule = $this->getEstimatedDeliveryRule();
        $time = $rule->last_dispatch_time;
        return $date . " " . $time;
    }

}