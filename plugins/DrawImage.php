<?php
namespace DrawEngine\Plugins;

class DrawImage extends \DrawEngine\Plugins\Plugin
{
    protected $images = [];
    public function init()
    {
        $this->engine->addCallback("draw_image", function ($file, $top, $left, $width=null, $height=null)
        {
            if(!isset($this->images[$file]))
            {
                $image = new \Imagick();
                $image->readimage($file);
                $this->images[$file] = $image;
            }

            $image = $this->images[$file];
            $geom = $image->getimagegeometry();
            if(!$width)
                $width = $geom["width"];
            if(!$height)
                $height = $geom["height"];
            \DrawEngine\DrawEngine::object()->draw->composite(\Imagick::COMPOSITE_OVER, $top, $left, $width, $height, $image);
        });
    }
} 