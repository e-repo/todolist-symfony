<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Doctrine\DBAL\Exception;

class RolesHelper
{
    private array $rolesHierarchy;
    private array $roles = [];

    /**
     * @param array $rolesHierarchy
     */
    public function __construct(array $rolesHierarchy)
    {
        $this->rolesHierarchy = $rolesHierarchy;
    }

    public function getRoles(): array
    {
        if([] !== $this->roles) {
            return $this->roles;
        }

        $roles = \array_keys($this->rolesHierarchy);
        \array_walk_recursive($this->rolesHierarchy, static function($val) use (&$roles) {
            $roles[] = $val;
        });

        return $this->roles = \array_unique($roles);
    }
}