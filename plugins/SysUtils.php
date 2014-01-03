<?php
namespace DrawEngine\Plugins;

class SysUtils extends \DrawEngine\Plugins\Plugin
{
    protected $images = [];

    public function init()
    {
        $this->engine->addCallback("list_files", function ()
        {
            $files = [];
            foreach (glob("gfx/*") as $id => $file)
            {
                $files[$id + 1] = $file;
            }
            return $files;
        });
    }
} 