<?php

declare(strict_types=1);

namespace Aality\RestrictAccess\Install;

use Db;

class InstallerFactory
{
    public static function create(): Installer
    {
        return new Installer(
            new FixturesInstaller(Db::getInstance())
        );
    }
}
