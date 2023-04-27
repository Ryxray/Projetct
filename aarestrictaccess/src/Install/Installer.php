<?php

declare(strict_types=1);

namespace Aality\RestrictAccess\Install;

use Db;
use Module;

/**
 * Class responsible for modifications needed during installation/uninstallation of the module.
 */
class Installer
{
    /**
     * @var FixturesInstaller
     */
    private $fixturesInstaller;

    public function __construct(FixturesInstaller $fixturesInstaller)
    {
        $this->fixturesInstaller = $fixturesInstaller;
    }

    /**
     * Module's installation entry point.
     *
     * @param Module $module
     *
     * @return bool
     */
    public function install(Module $module): bool
    {
        if (!$this->registerHooks($module)) {
            return false;
        }

        if (!$this->installDatabase()) {
            return false;
        }

        if (!$this->upgradeDatabase($module)) {
            return false;
        }

        $this->fixturesInstaller->install();

        return true;
    }

    /**
     * Module's uninstallation entry point.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
//        return $this->uninstallDatabase();
        # Do not uninstall for the moment
        return true;
    }

    /**
     * Install the database modifications required for this module.
     *
     * @return bool
     */
    private function installDatabase(): bool
    {
        $queries = [
            # Entity PostGallery
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'aality_exemple_table` (
                `id` INT NOT NULL AUTO_INCREMENT ,
                `id_something` INT NOT NULL ,
                `name` VARCHAR(255) NULL ,
                PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'aality_other_exemple_table` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `position` INT NOT NULL ,
                `name` VARCHAR(255) NOT NULL ,
                PRIMARY KEY (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
        ];

        return $this->executeQueries($queries);
    }

    /**
     * Uninstall database modifications.
     *
     * @return bool
     */
    private function uninstallDatabase(): bool
    {
        $queries = [];

        return $this->executeQueries($queries);
    }

    /**
     * Register hooks for the module.
     *
     * @param Module $module
     *
     * @return bool
     */
    private function registerHooks(Module $module): bool
    {
        $hooks = [
            # PS Hooks
            'displayFooter',
            'ActionOutputHTMLBefore',
            # Custom hooks
            'aalityDisplayMyHook',
        ];

        return (bool)$module->registerHook($hooks);
    }

    /**
     * A helper that executes multiple database queries.
     *
     * @param array $queries
     *
     * @return bool
     */
    private function executeQueries(array $queries): bool
    {
        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    private function upgradeDatabase(Module $module)
    {
        $queries = [];
//        if ($module->version == '1.0.1') {
//            $queries [] = '';
//        }

        return $this->executeQueries($queries);

    }
}
