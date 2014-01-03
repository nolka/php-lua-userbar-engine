<?php
namespace DrawEngine\Plugins;

class QueryInfo extends \DrawEngine\Plugins\Plugin
{
    protected $images = [];
    public function init()
    {
        $this->engine->assignVar("Query", $_REQUEST);
    }
} 