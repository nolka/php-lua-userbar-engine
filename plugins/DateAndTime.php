<?php
namespace DrawEngine\Plugins;

class DateAndTime extends \DrawEngine\Plugins\Plugin
{
    protected $images = [];

    public function init()
    {
        $this->engine->addCallback([
            "gmmktime",
            "gmdate",
            "mktime",
            "time"
        ]);
    }
} 