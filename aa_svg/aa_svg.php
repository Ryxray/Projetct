<?php

if (!defined('_PS_VERSION_'))
    exit;

class Aa_Svg extends Module
{
    public function __construct()
    {
        $this->name      = 'aa_svg';
        $this->tab       = 'front_office_features';
        $this->version   = '1.0.0';
        $this->author    = 'Aality';

        $this->ps_versions_compliancy = ['min' => '1.7.1', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->l('Aality :: SVG');
        $this->description = $this->l('Affichage des svg');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayAaSvg');
    }

    //{hook h='displayAaSvg' name='nom-du-fichier-svg'}
    public function hookDisplayAaSvg($params)
    {
	    # Paramètres du SVG passé dans le hook
        $svg_name   = isset($params['name'])   ? (string)$params['name']   : '';

        $svg_path = $this->context->shop->getTheme() . 'assets/img/svg/';

		if ($svg_name != '') {

            $svg_file = $svg_path . $svg_name . '.svg';

            if (is_file($svg_file)) {
                    $doc = new DOMDocument();
                    $doc->load($svg_file);

                    /** @var DOMElement $svg */
                    foreach ($doc->getElementsByTagName('svg') as $svg) {
                        $svg->setAttribute('class', trim("svg-{$svg_name}"));
                    }

                    $svg_data = $doc->saveXML($doc->documentElement);

                    $this->smarty->assign('svg', $svg_data);

                    return $this->display(__FILE__, 'views/templates/front/aasvg.tpl');
            }
        }

        return '';
    }
}
