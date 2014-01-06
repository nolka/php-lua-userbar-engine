<?php
namespace DrawEngine\Plugins;

class Sessions extends \DrawEngine\Plugins\Plugin
{
    public function init()
    {
        $this->engine->addCallback("session_start", "session_start");
        $this->engine->addCallback("session_destroy", "session_destroy");
        $this->engine->addCallback("set_session_var", function ($name, $value)
        {
            $_SESSION[$name] = $value;
        });
        $this->engine->addCallback("get_session_var", function ($name)
        {
            return getattr($_SESSION, $name);
        });
        if (isset($_SESSION))
            $this->engine->assignVar("Session", $_SESSION);
    }
} 