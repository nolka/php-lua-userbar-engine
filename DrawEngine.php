<?

namespace DrawEngine;

class DrawEngine
{

    protected $lua = null;
    /**
     * @var null|XImagickDraw
     */
    public $draw = null;
    protected $magic = null;
    protected $dimensions = array(320, 100);

    /**
     * @var DrawEngine
     */
    protected static $instance = null;

    protected $plugins = [];

    public $dbgMessages = [];

    public function __construct($startupScript, $w, $h, $color)
    {
        $this->lua = new \Lua();

        $this->lua->eval($startupScript);

        $this->magic = new \Imagick();
        $this->magic->newImage($w, $h, $color);
        $this->draw = new XImagickDraw();
        self::$instance = $this;
    }

    /**
     * @return DrawEngine|null
     */
    public static function object()
    {
        return self::$instance;
    }

    public function addCallback($callback, $params)
    {
        $this->lua->registerCallback($callback, $params);
    }

    public function assignVar($name, $value)
    {
        $this->lua->assign($name, $value);
    }

    public function registerMethods($optionalMethodsMap = null)
    {
        $imagickMethods = get_class_methods(get_class($this->draw));

        $disabledMethods = array();

        #var_dump($imagickMethods);
        foreach ($imagickMethods as $method)
        {
            $this->lua->registerCallback($method, array($this->draw, $method));
        }

        $this->lua->registerCallback("getstrokecolor", function ()
        {
            return DrawEngine::object()->draw->getStrokeColor()->getColor();
        });
        $this->lua->registerCallback("getfillcolor", function ()
        {
            return DrawEngine::object()->draw->getFillColor()->getColor();
        });

        $this->lua->registerCallback("setstrokecolor", function ($value)
            {
                if (is_string($value))
                {
                    DrawEngine::object()->draw->setStrokeColor($value);
                }
                if (is_array($value))
                {
                    $r = $value['r'];
                    $g = $value['g'];
                    $b = $value['b'];
                    DrawEngine::object()->draw->setStrokeColor('#' . DrawEngine::object()->rgbToHex($r, $g, $b));
                }
            }
        );

        $this->lua->registerCallback("setfillcolor", function ($value)
            {
                if (is_string($value))
                {
                    DrawEngine::object()->draw->setFillColor($value);
                }
                if (is_array($value))
                {
                    $r = $value['r'];
                    $g = $value['g'];
                    $b = $value['b'];
                    DrawEngine::object()->draw->setFillColor('#' . DrawEngine::object()->rgbToHex($r, $g, $b));
                }
            }
        );

        $this->lua->registerCallback("newcolor", function ($name)
        {
            return new ImagickPixel($name);
        });
        $this->lua->registerCallback("print", function ($text)
        {
            $message = array();
            foreach(func_get_args() as $arg)
            {
               $message[] = $arg;
            }
            DrawEngine::object()->dbgMessages[] = "print: ".implode(", ", $message);
        });

        $this->lua->registerCallback("array", function ()
        {
            return array(func_get_args());
        });
        $this->lua->registerCallback("dump", function ($data)
        {
            var_dump($data);
            $str = ob_get_clean();
            DrawEngine::object()->dbgMessages[] = "dump: ".$str;
        });

    }

    public function loadPlugins()
    {
        require_once "IPlugin.php";
        require_once "Plugin.php";

        foreach(glob(__DIR__."/plugins/*.php") as $pluginFile)
        {
            require_once $pluginFile;
            preg_match("#[^\/]+?$#", $pluginFile, $match);
            list($class_name, $ext) = explode(".", $match[0]);
            $class_name = '\DrawEngine\Plugins\\'.$class_name;
            $klass = new $class_name($this);
            $klass->init();
            $klass->load();
            $this->registerPlugin($class_name, $klass);
        }
    }

    public  function unloadPlugins()
    {
        foreach($this->plugins as $name => $instance)
        {
            $instance->unload();
        }
    }

    public function registerPlugin($name, $instance)
    {
    $this->plugins[$name] = $instance;
    }

    /**
     * Вызывается когда пользовательский код рисования выполнен.
     *  Введена для того, чтобы можно было реализовать вывод отладочных сообщений в простом виде.
     */
    public function afterDraw()
    {
        $this->draw->setfontsize(12);
        $this->draw->setfontfamily("Ubuntu");
        $this->draw->setfontweight(100);
        foreach($this->dbgMessages as $id => $msg)
        {
            $this->draw->annotation(2, 12*$id+14, $msg);
        }
    }

    public function debug($message)
    {
        $this->dbgMessages[] = "debug: ".$message;
    }

    public function rgbToHex($r, $g, $b)
    {
        $s = '';
        $h = '';
        $x = '0123456789ABCDEF';

        $s = array($r, $g, $b);
        if ($s)
        {
            for ($i = 0; $i < 3; $i++)
            {
                $h .= substr($x, $s[$i] >> 4, 1) . substr($x, $s[$i] & 15, 1);
            }
            return $h;
        } else
            return null;

        return false;
    }

    public function render()
    {
        $this->afterDraw();
        $this->magic->drawImage($this->draw);
        $this->magic->setImageFormat("png");
        header('Content-Type: image/png');
        echo $this->magic;
    }

    public function run($func, $args = null)
    {
        $this->lua->call($func, $args);
    }

}

class XImagickDraw extends \ImagickDraw
{

    private $xColors = array();

    public function setColor($name, $color)
    {
        $this->xColors[$name] = $color;
    }

    public function getColor($name)
    {
        if (isset($this->xColors[$name]))
        {
            return $this->xColors[$name];
        }
    }

}