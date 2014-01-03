<?php

namespace DrawEngine\Plugins;

interface IPlugin {
    public function __construct($engine);
    public function init();
    public function load();
    public function unload();
} 