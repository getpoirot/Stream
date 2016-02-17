<?php
namespace Poirot\Stream\Context\Http;

use Poirot\Std\AbstractOptions;

class HttpsContext extends HttpContext
{
    protected $wrapper = 'https';
}
 