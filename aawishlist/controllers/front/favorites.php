<?php

use PrestaShop\PrestaShop\Adapter\Entity\ProductAssembler;
use PrestaShop\PrestaShop\Adapter\Entity\ProductPresenterFactory;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;


/** @var Aawishlist $module */
class AaWishlistFavoritesModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(['meta_title' => $this->trans('Favorites', [], 'Modules.AaWishlist.Admin')]);
        $this->context->smarty->assign(['meta_description' => $this->trans('Decouvrez nos Favorites', [], 'Modules.AaWishlist.Admin')]);
        $this->context->smarty->assign(['tab_product' => $this->getPresentedProducts($this->SelectFromAaWishList(), 100, 0)]);

        $this->setTemplate('module:aawishlist/views/templates/front/favorites.tpl');
    }


    public function SelectFromAaWishList()
    {

        $select = 'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'aafavorite` WHERE `id_customer` = ' . $this->context->customer->id . ' AND `favorite`  = ' . 1 . ' ;';
        $productsIds = Db::getInstance()->executeS($select);
        return $productsIds;
    }

    private function getPresentedProducts($productIds, $limitation, $offset)
    {

        if (!empty($productIds)) {
            $assembler = new ProductAssembler(\Context::getContext());

            $presenterFactory = new ProductPresenterFactory(\Context::getContext());
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new ProductListingPresenter(
                new ImageRetriever(
                    \Context::getContext()->link
                ),
                \Context::getContext()->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                \Context::getContext()->getTranslator()
            );

            $presentedProducts = [];

            if (is_array($productIds)) {
                $limit = $limitation;
                $count = $offset;
                foreach ($productIds as $productId) {

                    if ($count > $limit) {
                        break;
                    }

                    $assembledProducts = $assembler->assembleProduct(['id_product' => $productId['id_product']]);
                    if ($assembledProducts && !empty($assembledProducts)) {
                        $presentedProducts[] = $presenter->present(
                            $presentationSettings,
                            $assembledProducts,
                            \Context::getContext()->language
                        );
                        $count++;
                    }
                }
            }

            return $presentedProducts;
        }
        return [];
    }

    protected function getBreadcrumbLinks()
    {
        $breadcrumb = [];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Home', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('index', true),
        ];

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Favorites', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getModuleLink('aawishlist', 'favorites'),
        ];


        return $breadcrumb;
    }
}
