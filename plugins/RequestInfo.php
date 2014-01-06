<?php
namespace DrawEngine\Plugins;

class RequestInfo extends \DrawEngine\Plugins\Plugin
{
    protected $images = [];

    public function init()
    {
        $this->engine->assignVar("Query", $_REQUEST);
        $this->engine->assignVar("Request", [
            "referer" => getattr($_SERVER, 'HTTP_REFERER'),
            "useragent" => getattr($_SERVER, 'HTTP_USER_AGENT'),
            "cookie" => getattr($_SERVER, 'HTTP_COOKIE'),
            "remoteIp" => getattr($_SERVER, 'REMOTE_ADDR'),
            "remoteAddr" => gethostbyaddr($_SERVER['REMOTE_ADDR']),
            "requestTime" => getattr($_SERVER, 'REQUEST_TIME'),
            "requestTimeF" => getattr($_SERVER, 'REQUEST_TIME_FLOAT'),
            "remotePort" => getattr($_SERVER, 'REMOTE_PORT'),
            "method" => getattr($_SERVER, 'REQUEST_METHOD'),
        ]);
    }
} 