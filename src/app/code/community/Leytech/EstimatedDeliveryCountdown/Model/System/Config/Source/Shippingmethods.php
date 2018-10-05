<?php
/**
 * @package    Leytech_EstimatedDeliveryCountdown
 * @author     Chris Nolan (chris@leytech.co.uk)
 * @copyright  Copyright (c) 2018 Chris Nolan
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Leytech_EstimatedDeliveryCountdown_Model_System_Config_Source_Shippingmethods
{
    /**
     * Return array of carriers.
     * If $isActiveOnlyFlag is set to true, will return only active carriers
     *
     * @param bool $isActiveOnlyFlag
     * @return array
     */
    public function toOptionArray($isActiveOnlyFlag=false)
    {
        // Get the shipping methods that are associated with estimated delivery rules
        $rules = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->getCollection();
        $associatedMethods = array();
        foreach ($rules->getData() as $rule) {
            $ruleMethods = explode(',',$rule['shipping_methods']);
            $associatedMethods = array_merge($associatedMethods, $ruleMethods);
        }

        $methods = array(array('value'=>'', 'label'=>''));
        $carriers = Mage::getSingleton('shipping/config')->getAllCarriers();
        foreach ($carriers as $carrierCode=>$carrierModel) {
            if (!$carrierModel->isActive() && (bool)$isActiveOnlyFlag === true) {
                continue;
            }
            $carrierMethods = $carrierModel->getAllowedMethods();
            if (!$carrierMethods) {
                continue;
            }
            $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');
            $methods[$carrierCode] = array(
                'label'   => $carrierTitle,
                'value' => array(),
            );
            foreach ($carrierMethods as $methodCode=>$methodTitle) {
                if (!in_array($carrierCode.'_'.$methodCode, $associatedMethods)) {
                    continue;
                }
                $methods[$carrierCode]['value'][] = array(
                    'value' => $carrierCode.'_'.$methodCode,
                    'label' => '['.$carrierCode.'] '.$methodTitle,
                );
            }
        }

        foreach ($methods as $key => $value) {
            if (count($value['value']) > 0) {
                $return[$key] = $value;
            }
        }
        return $return;
    }

}