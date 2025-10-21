<?php

namespace App\Support;

use App\Models\Family;
use Spatie\Permission\PermissionRegistrar;

class FamilyContext
{
    private ?int $familyId = null;

    private ?Family $family = null;

    public function __construct(private PermissionRegistrar $permissionRegistrar)
    {
    }

    public function set(?Family $family): void
    {
        $this->family = $family;
        $this->familyId = $family?->getKey();

        $this->syncPermissionTeam();
    }

    public function setId(?int $familyId): void
    {
        if ($familyId === $this->familyId) {
            return;
        }

        $this->familyId = $familyId;
        $this->family = null;

        $this->syncPermissionTeam();
    }

    public function currentFamily(): ?Family
    {
        if ($this->family !== null || $this->familyId === null) {
            return $this->family;
        }

        $this->family = Family::find($this->familyId);

        return $this->family;
    }

    public function currentFamilyId(): ?int
    {
        return $this->familyId;
    }

    public function clear(): void
    {
        $this->family = null;
        $this->familyId = null;

        $this->syncPermissionTeam();
    }

    private function syncPermissionTeam(): void
    {
        $this->permissionRegistrar->setPermissionsTeamId($this->familyId);
    }
}
