<?php
namespace Poirot\Stream\Wrapper\FileAccessControl;

use Poirot\Std\Struct\aDataOptions;

class ACWOptionsData 
    extends aDataOptions
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
