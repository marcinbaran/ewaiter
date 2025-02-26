<?php

namespace App\Models;

trait RoleHasTrait
{
    /**
     * Check if the user has at least one role.
     *
     * @param array|int|string $role
     *
     * @return bool
     */
    public function isOne($role)
    {
        if (null === $role) {
            return false;
        }
        foreach ($this->getArrayFrom($role) as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has all roles.
     *
     * @param array|int|string $role
     *
     * @return bool
     */
    public function isAll($role)
    {
        if (null === $role) {
            return false;
        }
        foreach ($this->getArrayFrom($role) as $role) {
            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the user has role.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        if (null == $this->roles) {
            return false;
        }
        if (is_string($this->roles)) {
            $this->roles = explode(',', $this->roles);
        }

        return in_array($role, $this->roles);
    }

    /**
     * Check if the user has role.
     *
     * @param string $role
     *
     * @return bool
     */
    public function isEndUserRole(): bool
    {
        $roles = [
            'ROLE_TABLE',
            'ROLE_ROOM',
            'ROLE_USER',
            'ROLE_GUEST',
        ];
        if (null == $this->roles) {
            return false;
        }
        if (is_string($this->roles)) {
            $this->roles = explode(',', $this->roles);
        }

        foreach ($roles as $role) {
            if (in_array($role, $this->roles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has roles.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRoles(array $roles): bool
    {
        if (null == $this->roles) {
            return false;
        }
        foreach ($roles as $role) {
            if (in_array($role, $this->roles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Attach role to a user.
     *
     * @param string $role
     *
     * @return self
     */
    public function attachRole(string $role): self
    {
        if (! $this->hasRole($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Detach role from a user.
     *
     * @param string $role
     *
     * @return self
     */
    public function detachRole(string $role): self
    {
        if ($this->hasRole($role)) {
            unset($this->roles[array_search($role, $this->roles)]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRolesName(): array
    {
        return array_map(function ($val) {
            return str_replace('ROLE_', '', $val);
        }, $this->roles);
    }

    /**
     * Get an array from argument.
     *
     * @param array|int|string $argument
     *
     * @return array
     */
    private function getArrayFrom($argument)
    {
        return (! is_array($argument)) ? preg_split('/ ?[,|] ?/', $argument) : $argument;
    }
}
