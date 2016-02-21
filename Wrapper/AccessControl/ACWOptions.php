<?php
namespace Poirot\Stream\Wrapper\AccessControl;

use Poirot\Std\Struct\AbstractOptionsData;

class ACWOptionsData extends AbstractOptionsData
{
    /**
     * @var ACWPermissions
     */
    protected $permissions;

    /**
     * @return ACWPermissions
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param ACWPermissions $permissions
     */
    public function setPermissions(ACWPermissions $permissions)
    {
        $this->permissions = $permissions;
    }
}
 