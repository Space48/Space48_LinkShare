<?php
class Space48_Linkshare_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()
            ->createBlock('space48_linkshare/linkshare')
            ->setTemplate('Space48/Linkshare.phtml');

        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function testAction()
    {
        Mage::getModel('space48_linkshare/Cron_Crontab')->createExport();
    }

    /**
     * Testing Update action
     *
     * Trying to en mass update all custom attributes
     *    'link_share_all_link',
     *    'link_share_product_link',
     *    'link_share_front_flag',
     *    'link_share_merchandiser_flag'
     */
    public function updateAction()
    {
        $eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
        $linkShareDeleted            = $eavAttribute->getIdByCode('catalog_product', 'link_share_deleted');
        $linkShareAllLink            = $eavAttribute->getIdByCode('catalog_product', 'link_share_all_link');
        $linkShareProductLink        = $eavAttribute->getIdByCode('catalog_product', 'link_share_product_link');
        $linkShareFrontFlag          = $eavAttribute->getIdByCode('catalog_product', 'link_share_front_flag');
        $linkShareMerchandiserFlag   = $eavAttribute->getIdByCode('catalog_product', 'link_share_merchandiser_flag');

        echo 'Link share deleted: ' . $linkShareDeleted . '<br />';
        echo 'Link share all link: ' . $linkShareAllLink . '<br />';
        echo 'Link share product link: ' . $linkShareProductLink . '<br />';
        echo 'Link share front flag: ' . $linkShareFrontFlag . '<br />';
        echo 'Link share Merchandiser flag: ' . $linkShareMerchandiserFlag . '<br />';
    }



    public function createExport()
    {
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect(
                array(
                    'name',
                    'short_description',
                    'description',
                    'price',
                    'image',
                    'status',
                    'manufacturer',
                    'url_path',
                    'link_share_deleted',
                    'link_share_all_link',
                    'link_share_product_link',
                    'link_share_front_flag',
                    'link_share_merchandiser_flag'),
                'inner'
            );

        Mage::getSingleton('core/resource_iterator')
            ->walk($products->getSelect(), array(array($this, 'productCallback')));
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

        /*$cats = $product->getCategoryIds();
        foreach ($cats as $cat) {
            $category = Mage::getModel('catalog/category')->load($cat);
            $parent = $category->getParentId();
            $parentCategory = Mage::getModel('catalog/category')->load($parent);
            Zend_Debug::dump($parentCategory->getName());
        }*/

        $path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();

        echo
            $product->getEntityId()                     . ' | ' .
            $product->getName()                         . ' | ' .
            $product->getSku()                          . ' | ' .
            Mage::getBaseUrl() . $product->getUrlPath() . ' | ' .
            $path                                       . ' | ' .
            $product->getShortDescription()             . ' | ' .
            $product->getPrice()                        . ' | ' .
            $product->getLinkShareDeleted()             . ' | ' .
            $product->getLinkShareAllLink()             . ' | ' .
            $product->getLinkShareProductLink()         . ' | ' .
            $product->getLinkShareFrontFlag()           . ' | ' .
            $product->getLinkShareMerchandiserFlag()    . ' | ' .
            Mage::app()->getStore(1)->getCurrentCurrencyCode();

    }
}