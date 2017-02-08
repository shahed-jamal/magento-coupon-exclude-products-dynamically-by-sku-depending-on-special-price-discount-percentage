<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Codilar
 * @package    Coupon
 * @copyright   Copyright (c) 2016 Codilar. (http://www.codilar.com)
 * @purpose     Main Observer
 * @author       Codilar Team
 **/
class Codilar_Coupon_Model_Observer
{

    public function __construct()
    {
//        $configValue = Mage::getStoreConfig('sectionName/groupName/fieldName');

    }
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Observer function
     * @param $observer
     */
    public function saveOrderDetailsInHistory($observer)
    {

        $event = $observer->getEvent();
        $couponCode = Mage::getStoreConfig('customconfig/coupon/couponCode');
        if ($event->getRule()->getData('code')==$couponCode){
            $discountPercentage = Mage::getStoreConfig('customconfig/coupon/discountPercentage');
            $oCoupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
            $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
            $item = $event->getItem();
            $simpleProduct=Mage::getModel('catalog/product')->loadByAttribute('sku', $item->getSku());
            $simpleProductPrice =  $simpleProduct->getPrice();
            $cartProductPrice = $item->getPrice();
            $discountPrice = $simpleProductPrice - $cartProductPrice;
            $discountPricePercentage = ($discountPrice/$simpleProductPrice) * 100;
            if ($discountPricePercentage <= $discountPercentage){
                $item->setOriginalCustomPrice($simpleProductPrice);
                $item->setCustomPrice($simpleProductPrice);
            } else {
                $skuForNotDis = $item->getData('sku');
                $actionForCoupon=unserialize($oRule->getData('actions_serialized'));
                foreach ($actionForCoupon as $condition) {
                    $i = 0;
                    foreach ($condition as $cdt) {
                        if ($cdt['attribute'] == 'sku') {
                            if ($condition[$i]['value']==''){
                                $condition[$i]['value'] = $skuForNotDis;
                            } else {
                                $condition[$i]['value'] = $condition[$i]['value'].', '.$skuForNotDis;
                            }
                        }
                        $i++;
                    }
                    $actionForCoupon['conditions'] = $condition;
                }
                $actionForCouponSerialize = serialize($actionForCoupon);
                $oRule->setData('actions_serialized',$actionForCouponSerialize)->save();
            }
        }
    }

    public function removeCustomPrice($observer){
        $controller = $observer->getControllerAction();
        if ($controller->getRequest()->getParam('remove') != 1) {
            $couponCode = Mage::getStoreConfig('customconfig/coupon/couponCode');
            $oCoupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
            $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
            $actionForCoupon=unserialize($oRule->getData('actions_serialized'));
            foreach ($actionForCoupon as $condition) {
                $i = 0;
                foreach ($condition as $cdt) {
                    if ($cdt['attribute'] == 'sku') {
                        $condition[$i]['value'] = '';
                    }
                    $i++;
                }
                $actionForCoupon['conditions'] = $condition;
            }
            $actionForCouponSerialize = serialize($actionForCoupon);
            $oRule->setData('actions_serialized',$actionForCouponSerialize)->save();
        }
        if ($controller->getRequest()->getParam('remove') == 1) {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $cartItems = $quote->getAllVisibleItems();

            foreach ($cartItems as $item) {
                $itemOrig = Mage::getModel('catalog/product')->load($item->getId());
//            $item->setPrice($ids);
//            $item->setBasePrice($ids);
                $item->setOriginalCustomPrice($itemOrig->getPrice());
                $item->setCustomPrice($itemOrig->getPrice());
            }
        }
    }

}