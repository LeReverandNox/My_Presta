<?php

class Video extends ObjectModel
{
    public $id_video;
    public $id_product;
    public $key;

    public static $definition = [
        "table" => "video",
        "primary" => "id_video",
        "multilang"=> false,
        "fields" => [
            "id_video" => [
                "type" => self::TYPE_INT,
                "validate" => "isUnsignedInt",
                "required" => true
            ],
            "id_product" => [
                "type" => self::TYPE_INT,
                "validate" => "isUnsignedInt",
                "required" => true
            ],
            "key" => [
                "type" => self::TYPE_STRING,
                "validate" => "isGenericName",
                "required" => true
            ]
        ]
    ];

    public static function findByProductId($product_id)
    {
    if (!Validate::isInt($product_id)) {
        return null;
    }

    $sql = 'select * from ' . _DB_PREFIX_ . self::$definition["table"] .' where id_product = ' . $product_id;
    if ($rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql)) {
        return self::hydrateCollection(__CLASS__, $rows)[0];
    }
    return [];
    }
}