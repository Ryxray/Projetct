<?php

namespace Acdis\Favorites\Model;


use Context;
use Dispatcher;

/**
 * Class Partners
 */
class Favorites extends \ObjectModel
{
    /** @var int $id_favorites */
    public $id_favorites;
    /** @var int $id_customer */
    public $id_customer;
    /** @var int $favorite */
    public $favorite;


    /** @var string $name */
    public $name;
    /** @var string $description */
    public $description;
    /** @var string $quote */
    public $quote;
    /** @var string $custom_zone */
    public $custom_zone;

    /** @var array $definition */
    public static $definition = [
        'table'     => 'aafavorite',
        'primary'   => '$id_favorites',
        'multilang' => true,
        'fields'    => [
            'id_favorites'    => ['type' => self::TYPE_INT, 'required' => true],
            'id_customer'    => ['type' => self::TYPE_INT, 'required' => true],
            'favorite'    => ['type' => self::TYPE_INT, 'required' => true],

            # Multilingual
            'name'          => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 128],
            'description'   => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'quote'         => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
            'custom_zone'   => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];

    private $products;
    private $categories;

    /**
     * @param null $id
     * @param null $id_lang
     * @param null $id_shop
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        # Real values
        if ($this->id) {

            $this->products      = $this->getProducts();
            $this->categories      = $this->getCategories();
        } else {
            $this->products      = [];
            $this->categories      = [];
        }
    }

    /**
     * @param int $idProduct
     *
     * @return Favorites
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function getProductFavorites($idProduct)
    {
        $dbQuery = new \DbQuery();
        $dbQuery->select('`id_favorites`')
            ->from('aafavorites')
            ->where('id_product = ' . (int)$idProduct);
        $id = \Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->getValue($dbQuery);

        return new self((int)$id);
    }

    /**
     *
     * @return array
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getProducts()
    {
        $productsId = [];
        $dbQuery    = new \DbQuery();
        $dbQuery->select('`id_product`')
                ->from('aafavorites')
                ->where('id_favorites = ' . $this->id_favorites);
        $results = \Db::getInstance(_PS_USE_SQL_SLAVE_)
                      ->executeS($dbQuery);
        foreach ($results as $row) {
            if (isset($row['id_product'])) {
                $productsId[] = $row['id_product'];
            }
        }

//        $dbQuery->select('`id_category`')
//            ->from('aafavorites')
//            ->where('id_favorites = ' . $this->id_partner);
//        $results = \Db::getInstance(_PS_USE_SQL_SLAVE_)
//            ->executeS($dbQuery);
//        foreach ($results as $row) {
//            if (isset($row['id_category'])) {
//                $cat = new \Category($row['id_category']);
//                foreach ($cat->getProductsWs() as $pId) {
//                    $productsId[] = $pId['id'];
//                }
//            }
//        }

        return array_unique($productsId);
    }



    public function toArray()
    {
        return [
            'id'            => $this->id,
            'id_favorites'    => $this->id_favorites,
            'name'          => $this->name,
            'description'   => $this->description,
            'quote'         => $this->quote[1],
            'custom_zone'   => $this->custom_zone[1],
            'products'      => $this->products,
            'categories'      => $this->categories,
        ];
    }

    public function getUrl()
    {       //ATTENTION CHEMIN PAS BON
        return Context::getContext()->link->getModuleLink('aawishlist', 'favorites');
//        return Context::getContext()->link->getModuleLink('aawishlist', 'favorites', ['id_favorites' => (int)$this->id . '_ambassadeurs-' . \Tools::str2url($this->name[1])]);
    }
}
