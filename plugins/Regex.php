<?php
namespace DrawEngine\Plugins;

class Regex extends \DrawEngine\Plugins\Plugin
{
    public function init()
    {
        $this->engine->addCallback("re_match", function ($pattern, $test_str)
        {
            return boolval(preg_match($pattern, $test_str));
        });

        $this->engine->addCallback("re_match_get", function($pattern, $string){
            $matches = [];
            preg_match_all($pattern, $string, $matches);
            return $matches;
        });
    }
} 