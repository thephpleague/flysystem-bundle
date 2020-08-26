<?php

namespace Tests\League\FlysystemBundle\Fakes;

use League\Flysystem\MountManager;

final class FakeMountManagerUser
{
    /** @var MountManager */
    private $mountManager;

    public function __construct(MountManager $mountManager)
    {
        $this->mountManager = $mountManager;
    }

    public function getMountManager(): MountManager
    {
        return $this->mountManager;
    }
}
