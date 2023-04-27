<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class aaWishList extends Module
{

    public function __construct()
    {
        $this->name = 'aawishlist';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Aality';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Aa Wish List');
        $this->description = $this->l('Permet de garder en favoris vos produits préferer');

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');

        if (!Configuration::get('AAWISHLIST_PAGENAME')) {
            $this->warning = $this->l('Aucun nom fourni');
        }
    }


    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('displayProductActions') ||
            !$this->registerHook('header') ||
            !$this->registerHook('displayTop') ||
            !$this->registerHook('displayFavoritesSvg') ||
            !$this->registerHook('moduleRoutes') ||
            !Configuration::updateValue('AAWISHLIST_PAGENAME', 'Mentions légales')
        ) {
            return false;
        }

        $sqlQueries = ' CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'aafavorite` (
            `id_product` int(10) unsigned NOT NULL,
            `id_customer` int(10) unsigned NOT NULL,
            `favorite` boolean not null default 0,
            PRIMARY KEY (`id_product`, `id_customer`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';

        if (Db::getInstance()->execute($sqlQueries) == false) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {

        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'aafavorite`';
        if (Db::getInstance()->execute($sql) == false) {
            return false;
        }

        if (!parent::uninstall() ||
            !Configuration::deleteByName('AAWISHLIST_PAGENAME')
        ) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('btnSubmit')) {
            $pageName = strval(Tools::getValue('AAWISHLIST_PAGENAME'));

            if (
                !$pageName ||
                empty($pageName)
            ) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('AAWISHLIST_PAGENAME', $pageName);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->displayForm();
    }


    public function hookDisplayHeader()
    {

        $this->context->controller->addJs($this->_path . 'views/js/aawishlist.js');
        $this->context->controller->registerStylesheet(
            'aawishlist',
            $this->_path . 'views/css/aawishlist.css',
            ['server' => 'remote', 'position' => 'head', 'priority' => 150]
        );

    }

    public function hookDisplayTop()
    {
        return $this->display(__FILE__, 'views/templates/front/favoritesbuttons.tpl');
    }

    public function hookDisplayFavoritesSvg($params)
    {
        # Paramètres du SVG passé dans le hook
        $svg_name   = isset($params['name'])   ? (string)$params['name']   : '';

        $svg_path = __DIR__.'/views/img/';

        if ($svg_name != '') {

            $svg_file = $svg_path . $svg_name . '.svg';

            if (is_file($svg_file)) {
                $doc = new DOMDocument();
                $doc->load($svg_file);

                /** @var DOMElement $svg */
                foreach ($doc->getElementsByTagName('svg') as $svg) {
                    $svg->setAttribute('class', trim("svg-{$svg_name}"));
                    $svg->setAttribute('data-id-name', trim("svg-{$svg_name}"));
                }

                $svg_data = $doc->saveXML($doc->documentElement);

                $this->smarty->assign('svg', $svg_data);

                return $this->display(__FILE__, 'views/templates/front/showsvg.tpl');
            }
        }

        return '';
    }

    public function hookModuleRoutes($params)
    {
        return [
            'module-aawishlist-favorites' => [
                'controller' => 'favorites',
                'rule' => 'favorites',
                'keywords' => [
                    'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                    'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'aawishlist'
                ]
            ],
        ];

    }

    public function displayForm()
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Configuration value'),
                        'name' => 'AAWISHLIST_PAGENAME',
                        'size' => 20,
                        'required' => true
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'btnSubmit'
                ]
            ],
        ];

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $helper->default_form_language = $defaultLang;

        $helper->fields_value['AAWISHLIST_PAGENAME'] = Configuration::get('AAWISHLIST_PAGENAME');

        return $helper->generateForm([$form]);
    }

    public function hookDisplayProductActions($params)
    {

        $id_product_sql= $params["product"]["id_product"];
        $id_customer = $this->context->customer->id;

        $select = 'SELECT `favorite` FROM `' . _DB_PREFIX_ . 'aafavorite` WHERE `id_product` = ' . $id_product_sql . ' AND `id_customer`  = ' . $id_customer . ' ;';

        $this->smarty->assign('svg', $params);
        $this->context->smarty->assign([
            'product' => $params["product"],
            'ns_page_name' => Configuration::get('AAWISHLIST_PAGENAME'),
            'ns_page_link' => $this->context->link->getModuleLink('aawishlist', 'display'),
            'is_favorite' => Db::getInstance()->getValue($select),
        ]);

        return $this->display(__FILE__, 'aawishlist.tpl');
    }

}
