<?php

declare(strict_types=1);

namespace Aality\RestrictAccess\Install;

use Db;

/**
 * Installs data fixtures for the module.
 */
class FixturesInstaller
{
    /**
     * @var Db
     */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function install(): void
    {
        # Todo populate our database tables
        //$this->insertExemple('toto');
    }

    private function insertExemple($param): void
    {
        $this->db->insert(
            'table', [
            'field1' => $param,
            'field2' => 'value',
        ]);
    }
}
