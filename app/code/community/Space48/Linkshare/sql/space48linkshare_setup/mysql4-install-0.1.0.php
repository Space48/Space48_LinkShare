<?php

$installer = $this;
$installer->startSetup();

// Start adding our new attributes to the correct new tab
$installer->addAttribute('catalog_product', 'link_share_deleted', array(
    'group'    => 'Link share affiliate',
    'label'    => 'Is Deleted Flag?',
    'type'     => 'int',
    'input'    => 'boolean',
    'visible'  => true,
    'required' => false,
    'position' => 1,
    'global'   => 'Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL',
    'note'     => 'Is product marked as deleted?'
));

$installer->addAttribute('catalog_product', 'link_share_all_link', array(
    'group'    => 'Link share affiliate',
    'label'    => 'Is All Flag?',
    'type'     => 'int',
    'input'    => 'boolean',
    'visible'  => true,
    'required' => false,
    'position' => 2,
    'global'   => 'Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL',
    'note'     => 'Is All flag?'
));

$installer->addAttribute('catalog_product', 'link_share_product_link', array(
    'group'    => 'Link share affiliate',
    'label'    => 'Is Product Link Flag?',
    'type'     => 'int',
    'input'    => 'boolean',
    'visible'  => true,
    'required' => false,
    'position' => 3,
    'global'   => 'Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL',
    'note'     => 'Is product link flag?'
));

$installer->addAttribute('catalog_product', 'link_share_front_flag', array(
    'group'    => 'Link share affiliate',
    'label'    => 'Is Store Front Flag?',
    'type'     => 'int',
    'input'    => 'boolean',
    'visible'  => true,
    'required' => false,
    'position' => 4,
    'global'   => 'Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL',
    'note'     => 'Is Store Front flag?'
));

$installer->addAttribute('catalog_product', 'link_share_merchandiser_flag', array(
    'group'    => 'Link share affiliate',
    'label'    => 'Is Merchandiser Flag?',
    'type'     => 'int',
    'input'    => 'boolean',
    'visible'  => true,
    'required' => false,
    'position' => 5,
    'global'   => 'Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL',
    'note'     => 'Is Merchandiser flag?'
));
// End of adding in our new attribute

$sqlDeleted = "INSERT INTO
            catalog_product_entity_int (entity_type_id, attribute_id, store_id, entity_id, value)
        SELECT 4, (
            SELECT
                attribute_id
            FROM
                eav_attribute eav
            WHERE
                eav.entity_type_id = 4
            AND
                eav.attribute_code = 'link_share_deleted'
                )
        , 0, catalog_product_entity.entity_id, 0 FROM catalog_product_entity
        ON DUPLICATE KEY UPDATE value=value;";

$sqlLink = "INSERT INTO
            catalog_product_entity_int (entity_type_id, attribute_id, store_id, entity_id, value)
        SELECT 4, (
            SELECT
                attribute_id
            FROM
                eav_attribute eav
            WHERE
                eav.entity_type_id = 4
            AND
                eav.attribute_code = 'link_share_all_link'
                )
        , 0, catalog_product_entity.entity_id, 0 FROM catalog_product_entity
        ON DUPLICATE KEY UPDATE value=value;";

$sqlProductLink = "INSERT INTO
            catalog_product_entity_int (entity_type_id, attribute_id, store_id, entity_id, value)
        SELECT 4, (
            SELECT
                attribute_id
            FROM
                eav_attribute eav
            WHERE
                eav.entity_type_id = 4
            AND
                eav.attribute_code = 'link_share_product_link'
                )
        , 0, catalog_product_entity.entity_id, 0 FROM catalog_product_entity
        ON DUPLICATE KEY UPDATE value=value;";

$sqlFront = "INSERT INTO
            catalog_product_entity_int (entity_type_id, attribute_id, store_id, entity_id, value)
        SELECT 4, (
            SELECT
                attribute_id
            FROM
                eav_attribute eav
            WHERE
                eav.entity_type_id = 4
            AND
                eav.attribute_code = 'link_share_front_flag'
                )
        , 0, catalog_product_entity.entity_id, 0 FROM catalog_product_entity
        ON DUPLICATE KEY UPDATE value=value;";

$sqlMerchandiser = "INSERT INTO
            catalog_product_entity_int (entity_type_id, attribute_id, store_id, entity_id, value)
        SELECT 4, (
            SELECT
                attribute_id
            FROM
                eav_attribute eav
            WHERE
                eav.entity_type_id = 4
            AND
                eav.attribute_code = 'link_share_merchandiser_flag'
                )
        , 0, catalog_product_entity.entity_id, 0 FROM catalog_product_entity
        ON DUPLICATE KEY UPDATE value=value;";

$installer->run($sqlDeleted);
$installer->run($sqlLink);
$installer->run($sqlProductLink);
$installer->run($sqlFront);
$installer->run($sqlMerchandiser);


$installer->endSetup();