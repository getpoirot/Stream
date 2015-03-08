<?php
namespace Poirot\Stream\Wrapper\AccessControl;

class ACWPermissions 
{
    protected $hasRead  = true;
    protected $hasWrite = true;

    function grantRead()
    {
        $this->hasRead = true;

        return $this;
    }

    function revokeRead()
    {
        $this->hasRead = false;

        return $this;
    }

    function hasReadAccess()
    {
        return $this->hasRead;
    }

    function grantWrite()
    {
        $this->hasWrite = true;

        return $this;
    }

    function revokeWrite()
    {
        $this->hasWrite = false;

        return $this;
    }

    function hasWriteAccess()
    {
        return $this->hasWrite;
    }
}
