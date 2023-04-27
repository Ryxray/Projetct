<?php

declare(strict_types=1);

namespace Aality\CustomText\Hook;

use aaCustomText;
use Context;
use Configuration;

/**
 * Class responsible for hook executions
 */
class HookExecutor
{
    /**
     * @var $this null
     */
    private static $instance = null;
    /**
     * @var string
     */
    private $hook_name;
    /**
     * @var Context
     */
    private $context;
    /**
     * @var array
     */
    private $params;
    /**
     * @var aaCustomText
     */
    private $module;

    /**
     * Hook constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param string $hook_name
     * @param aaCustomText $module
     * @param array $params
     *
     * @return
     */
    public static function execute(string $hook_name, aaCustomText $module, array $params)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        self::$instance->hook_name = $hook_name;
        self::$instance->module = $module;
        self::$instance->params = $params;
        self::$instance->context = Context::getContext();
        return self::$instance->$hook_name($params);
    }

    public function hookDisplayFooter($params)
    {

    }

    public function hookdisplayFooterCategory(array $params) {
         return $this->module->fetch('module:' . $this->module->name . '/views/templates/front/textcustomproduct.tpl');
    }

    /**
     * @param array $params
     *
     * @return string
     */

    public function hookDisplayHome(array $params): string
    {
        $html = '';
        $condition = true;

        if ($condition) {
            $this->context->smarty->assign([
                'column' => [
                    'firstColumn' => [
                        '1' => Configuration::getMultiple(['CUSTOM_TEXT_TITRE_1', 'CUSTOM_TEXT_DESCRIPTION_1']),
                        '2' => Configuration::getMultiple(['CUSTOM_TEXT_TITRE_2', 'CUSTOM_TEXT_DESCRIPTION_2'])
                    ],
                    'secondColumn' => [
                        '1' => Configuration::getMultiple(['CUSTOM_TEXT_TITRE_3', 'CUSTOM_TEXT_DESCRIPTION_3']),
                        '2' => Configuration::getMultiple(['CUSTOM_TEXT_TITRE_4', 'CUSTOM_TEXT_DESCRIPTION_4'])
                    ]
                ]
            ]);
            $html = $this->module->fetch('module:' . $this->module->name . '/views/templates/front/exemple.tpl');
        }

        return $html;
    }


}
