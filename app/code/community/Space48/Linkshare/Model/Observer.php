<?php

class Space48_Linkshare_Model_Observer
{
    /**
     * Check for linkshare affiliate link data to set cookie for
     * selective tracking pixel display on Space48/linkshare.phtml
     *
     * @param type $observer
     */
    public function linkshareAffiliateTracking($observer)
    {
        $request = $observer->getEvent()->getData('front')->getRequest();

        if (isset($request->siteID) && (strlen($request->siteID) == 34)) {
            Mage::getModel('core/cookie')->set('affiliatenetwork', 'linkshare', 86400*90);//90 day cookie
        }
    }
}