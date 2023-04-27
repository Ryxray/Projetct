<?php

declare(strict_types=1);

use Aality\RestrictAccess\Configuration\ConfigurationHelper;
use Aality\RestrictAccess\Hook\HookExecutor;
use Aality\RestrictAccess\Install\InstallerFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class AaRestrictAccess extends Module
{
    /**
     * @var ConfigurationHelper
     */
    private $configuration;

    public function __construct()
    {
        $this->name                   = 'aarestrictaccess';
        $this->author                 = 'Aality SAS';
        $this->version                = '1.0.0';
        $this->ps_versions_compliancy = ['min' => '1.7.7.7', 'max' => _PS_VERSION_];
        $this->tab                    = 'front_office_features';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Aality - restric access');
        $this->description = $this->l('Exemple module');

        # Load Configuration Class
        $this->configuration = new Aality\RestrictAccess\Configuration\ConfigurationHelper($this);
    }

    /**
     * Do not modify
     *
     * @return bool
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $installer = InstallerFactory::create();

        return $installer->install($this);
    }

    /**
     * Do not modify
     *
     * @return bool
     */
    public function uninstall()
    {
        $installer = InstallerFactory::create();

        return $installer->uninstall() && parent::uninstall();
    }

    /**
     * This method handles the module's configuration page
     *
     * @return string The page's HTML content
     */
    public function getContent()
    {
        # Call our helper class to handle this.
        return $this->configuration->getForm();
    }

    /**
     * This is magic method, used to call Hooks with our helper dedicated class
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return null
     */
    public function __call(string $name, array $arguments)
    {
        # If method exists and is a hook
        if (method_exists(HookExecutor::class, $name) && substr($name, 0, 4) === 'hook') {
            return HookExecutor::execute($name, $this, $arguments[0] ?? null);
        }
        return null;
    }

}
