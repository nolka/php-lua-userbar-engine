<?php
namespace DrawEngine\Plugins;

class GifProcessing extends \DrawEngine\Plugins\Plugin
{
    protected $images = [];
    public function init()
    {
        $this->engine->addCallback("load_gif", function ($file_name)
        {
            if(!file_exists("gfx/".$file_name))
                $this->engine->debug("missed ".$file_name);
            $imagick = new \Imagick("gfx/".$file_name);
            $imagick = $imagick->coalesceimages();
            do{
                $this->engine->layers;
            } while($imagick->nextimage());
        });
    }
} 