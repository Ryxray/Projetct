<?php

declare(strict_types=1);

namespace Aality\RestrictAccess\Hook;

use AaRestrictAccess;
use Context;
use function Clue\StreamFilter\register;

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
     * @var AaRestrictAccess
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
     * @param AaRestrictAccess $module
     * @param array $params
     *
     * @return
     */
    public static function execute(string $hook_name, AaRestrictAccess $module, array $params)
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

    /**
     * @param array $params
     *
     * @return string
     */
    public function hookAalityDisplayMyHook(array $params): string
    {
        $html = '';
        $condition = true;
        if ($condition) {
            $this->context->smarty->assign([
                'someData' => ['foo' => 'bar']
            ]);
            $html = $this->module->fetch('module:' . $this->module->name . '/views/templates/front/exemple.tpl');
        }

        return $html;
    }


    public function hookActionOutputHTMLBefore(array $params)
    {
        $context = Context::getContext();
        $url_connexion = $context->smarty->tpl_vars[urls]->value[pages][authentication];
        $url_formulaire = $context->smarty->tpl_vars[urls]->value[pages][register];
        $current_url = $context->smarty->tpl_vars[urls]->value[current_url];


        if ($context->customer->logged !== true) {
            if ($current_url !== $url_connexion && $current_url !== $url_formulaire) {
                header('Location:' . $url_connexion);
            }
        }
    }
}
