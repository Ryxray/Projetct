<?php

/** @var Aawishlist $module */

class AaWishlistWishlistAjaxModuleFrontController extends ModuleFrontController
{

    public $module;

    public function display()
    {

    }

    public function displayAjaxAddToWishlist()
    {

        $idProduct = Tools::getValue('idProduct');
        $idCustomer = Tools::getValue('idCustomer');

        $select = 'SELECT `favorite` FROM `' . _DB_PREFIX_ . 'aafavorite` WHERE `id_product` = ' . $idProduct . ' AND `id_customer`  = ' . $idCustomer . ' ;';

        $favoriteBoolean = Db::getInstance()->getValue($select);


        if ($favoriteBoolean == 1) {
            $delete = 'DELETE  FROM `' . _DB_PREFIX_ . 'aafavorite` WHERE `id_product` = ' . $idProduct . '  AND `id_customer` = ' . $idCustomer . '  ;';
            Db::getInstance()->execute($delete);
            $resultFavorite = 2;
        } else if (!$favoriteBoolean) {
            $add = 'INSERT INTO `' . _DB_PREFIX_ . 'aafavorite` ( `id_product`, `id_customer`, `favorite`) VALUES (' . $idProduct . ', ' . $idCustomer . ',' . 1 . ' )';
            Db::getInstance()->execute($add);
            $resultFavorite = Db::getInstance()->getValue($select);
        }



        $this->ajaxRender(json_encode(['success' => $resultFavorite]));
        exit;

    }

}
