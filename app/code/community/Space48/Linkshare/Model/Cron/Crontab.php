<?php
/**
 * Class Space48_Linkshare_Model_Cron_Crontab
 */
class Space48_Linkshare_Model_Cron_Crontab
{
    /** @var  object $file Instance of Varien_Io_File */
    protected $file;

     /**
     * Create Linkshare file and act as construct to push to other functions
     *   where required. Such as Upload to remote FTP
     *
     *  Manufacturer has been removed as not all products have this attribute associated with them.
     */
    public function createExport()
    {
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('status', array('eq' => '1'))
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect(
                array(
                    'name',
                    'short_description',
                    'description',
                    'price',
                    'special_price',
                    'image',
                    'status',
                    'url_path',
                    'link_share_deleted',
                    'link_share_all_link',
                    'link_share_product_link',
                    'link_share_front_flag',
                    'link_share_merchandiser_flag',
                    'visibility',
                    'manufacturer'
                    ),
                'inner'

            );

        $file = new Varien_Io_File();
        $file->open(array('path' => Mage::getBaseDir() . '/var/'));
        $file->streamOpen('linkshare.txt', 'w+');
        $file->streamLock(true);

        $this->file = $file;

        // write the header row into the feed
        $this->file->streamWrite('HDR|38548|Aphrodite1994|' . date('yyy-mm-dd/hh:mm:ss') . '\r\n');

        Mage::getSingleton('core/resource_iterator')
            ->walk($products->getSelect(), array(array($this, 'productCallback')));

        // write the footer row into the feed.
        $this->file->streamWrite('TRL|' . $products->count() . '|\r\n');

        $file->close();

        $this->uploadFile(Mage::getBaseDir() . '/var/linkshare.txt');
    }

    /**
     * Resource/Iterator call back function for processing collection
     *  data. Deals with writing to the stream open via the Varien object
     *
     * @param $args
     */
    function productCallback($args)
    {
        $product = Mage::getModel('catalog/product');
        $product->setData($args['row']);

        $cats = $product->getCategoryIds();

        // load stock levels.
        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());


            if (!in_array(array(20, 22, 24, 26, 28, 30, 32), $cats)) {

                if ($product->getData('type_id') == 'configurable' && $product->getData('visibility') != 1) {
                    $categoryName = array();
                    foreach ($cats as $cat) {
                        $category = Mage::getModel('catalog/category')->load($cat);
                        $parent = $category->getParentId();
                        $parentCategory = Mage::getModel('catalog/category')->load($parent);
                        $categoryName[] = ($parentCategory->getName());
                    }

                    if ($product->getLinkShareDeleted() == 1) {
                        $linkshareDeleted = 'Y';
                    } else {
                        $linkshareDeleted = 'N';
                    }

                    if ($product->getLinkShareAllLink() == 1) {
                        $linkshareAllLink = 'Y';
                    } else {
                        $linkshareAllLink = 'N';
                    }

                    if ($product->getLinkShareProductLink() == 1) {
                        $linkshareProductLink = 'Y';
                    } else {
                        $linkshareProductLink = 'N';
                    }

                    if ($product->getLinkShareFrontFlag() == 1) {
                        $linkshareFrontFlag = 'Y';
                    } else {
                        $linkshareFrontFlag = 'N';
                    }

                    if ($product->getLinkShareMerchandiserFlag() == 1) {
                        $linkshareMerchandiser = 'Y';
                    } else {
                        $linkshareMerchandiser = 'N';
                    }

                    if ($product->getImage()) {
                        $imagePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
                    } elseif ($product->getImage() == 'no_selection') {
                        $imagePath = '';
                    } else {
                        $imagePath = '';
                    }

                    if ($product->getShortDescription()) {
                        $shortDescription = '' . strip_tags(str_replace(array("\r\n", "\r", "\n"), null, $product->getShortDescription())) . '';
                    } else {
                        $shortDescription = '';
                    }

                    /*
                    if(isset($categoryName[0])){
                        if ($categoryName[0] == 'Default Category' || $categoryName[0] == 'Sale') {
                            $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
                            $attributeSetModel->load($product->getAttributeSetId());
                            $attributeSetName  = $attributeSetModel->getAttributeSetName();

                            $categoryName[0] = $attributeSetName;
                        }
                    } else {
                        $categoryName[0] = '';
                    }*/

                    $attributeSet = Mage::getModel('eav/entity_attribute_set')
                        ->load($product->getAttributeSetId())
                        ->getAttributeSetName();

                    if ($product->getSpecialPrice()) {
                        $specialPrice = $product->getSpecialPrice();
                    } else {
                        $specialPrice = '';
                    }

                    $this->file->streamWrite(
                        $product->getEntityId() . '|' .
                        $product->getName() . '|' .
                        $product->getSku() . '|' .
                        $attributeSet . '|' .
                        '|' .
                        Mage::getBaseUrl() . $product->getUrlPath() . '|' .
                        $imagePath   . '|' .
                        '|' .
                        $shortDescription . '|' .
                        '|' .
                        '|' .
                        '|' .
                        '|' .
                        round($product->getPrice()) . '|' .
                        $specialPrice . '|' .
                        '|' .
                        $product->getAttributeText('manufacturer') .'|' .
                        '|' .
                        $linkshareDeleted . '|' .
                        '|' .
                        $linkshareAllLink . '|' .
                        '|' .
                        '|' .
                        '|' .
                        '|' .
                        '|' .
                        '|' .
                        $linkshareProductLink . '|' .
                        $linkshareFrontFlag . '|' .
                        $linkshareMerchandiser . '|' .
                        Mage::app()->getStore(1)->getCurrentCurrencyCode() . "|\r\n"
                    );
                }
            }
    }

    /**
     * Upload the file to the remote FTP server
     *
     * @param string $file Full path to the file to be uploaded.
     *
     * @return bool status of the upload
     */
    public function uploadFile($file)
    {
        $userInfo = $this->_getFtpDetails();
        $upload   = new Varien_Io_Ftp();
        $upload->open(
            array(
                'host'     => $userInfo['ftphost'],
                'user'     => $userInfo['ftpuser'],
                'password' => $userInfo['ftppassword']
            )
        );

        $date = date('Ymd');
        $upload->write('38548_nmerchandis' . $date . '.txt', $file);
        $upload->close();
    }

    /**
     * Get all linkshare configuration data.
     *
     * @return array All data from system config for linkshare
     */
    protected function _getFtpDetails()
    {
        return Mage::getStoreConfig('Space48_Linkshare/linkshare');
    }
}