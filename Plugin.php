<?php

namespace DrawEngine\Plugins;

class Plugin implements IPlugin{
    /**
     * @var \DrawEngine\DrawEngine
     */
    protected $engine = null;
    public function __construct($engine)
    {
        $this->engine = $engine;
    }

    public function init(){}
    public function load(){}
    public function unload(){}
} 