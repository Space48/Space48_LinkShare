<?php
class Space48_Linkshare_Block_Linkshare extends Mage_Core_Block_Template
{
    /**
     * Helper function to load the MID setting from the adminHTML section
     *  of Magento system.
     *
     * @return int mixed MID for the LinkShare system
     */
    public function getMerchantId()
    {
        $mid = Mage::getStoreConfig('Space48_Linkshare/linkshare/mid');
        return $mid;
    }

    /**
     * Helper function to return the last order ID from the current
     *  users session.
     *
     * @return int ID of the last order
     */
    public function getLastOrderId()
    {
        $lastOrderId = Mage::getModel('checkout/session')->getLastRealOrderId();
        return $lastOrderId;
    }

   protected function getLastMagentoOrderId()
   {
       $orderId = Mage::getModel('checkout/session')->getLastOrderId();
       return $orderId;
   }

    /**
     * Helper function to return an instance of the order based
     *  on the users current last order ID.
     *
     * @param int $orderId ID of the last order
     *
     * @return Mage_Core_Model_Abstract instance of the last order object.
     */
    public function getLastOrder($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        return $order;
    }

    /**
     * Get the stores currency code
     *
     * @return string currency code
     */
    public function getCurrencyCode()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Get the HTTP protocol to use when integrating with PIXEL tracking
     *
     * @return string HTTP/S protocol to use.
     */
    public function getLinkshareProtocol()
    {
        return Mage::getStoreConfig('Space48_Linkshare/linkshare/protocol');
    }

    /**
     * Helper function to return all required information about the sale
     *   - SKU List URL encoded of all product SKU's
     *   - Amount total price of the sale
     *   - Quantity total quantity of the sale
     *   - Product list of all the products sold in that sale.
     *
     * @return array
     */
    public function getSkus()
    {
        $sku      = '';
        $amount   = '';
        $quantity = '';
        $product  = '';

        $order = $this->getLastOrder($this->getLastMagentoOrderId());
        foreach ($order->getAllItems() as $item) {
        if ($item->getPrice() > 0) {
                if ($sku != '') { $sku .= '|';}
                    $sku .= urlencode($item->getSku());
                if ($amount != '') { $amount .= '|';}
                    $amount .= round(($item->getData('base_price') * $item->getQtyOrdered()) * 100);
                if ($quantity != '') { $quantity .= '|';}
                    $quantity .= round($item->getQtyOrdered());
                if ($product != '') { $product .= '|';}
                    $product .= urlencode($item->getName());
            }
        }

        // check to see if there is a discount applied
        if ($order->getBaseDiscountAmount() != '0.0000') {
            $sku      .= '|Discount';
            $quantity .= '|0';
            $amount   .= '|' . round($order->getDiscountAmount() * 100);
            $product  .= '|Discount';
        } 

        return array(
            'sku'      => $sku,
            'amount'   => $amount,
            'quantity' => $quantity,
            'product'  => $product,
            'currency' => 'GBP'
	    );
    }

}
