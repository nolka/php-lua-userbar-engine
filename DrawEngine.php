<?

namespace DrawEngine;

class DrawEngine
{
    /**
     * @var \Lua
     */
    protected $lua = null;
    /**
     * @var XImagickDraw[]
     */
    public $layers = [];
    /**
     * @var int[]
     */
    public $layersDelays = [];
    /**
     * @var int
     */
    public $activeLayer = 1;
    /**
     * @var \Imagick|null
     */
    protected $magic = null;
    /**
     * @var array
     */
    protected $dimensions = array(320, 100);

    /**
     * @var DrawEngine
     */
    protected static $instance = null;

    /**
     * @var array
     */
    protected $plugins = [];

    /**
     * @var array
     */
    public $dbgMessages = [];

    protected $width;
    protected $height;

    public function __construct($startupScript, $w, $h, $color)
    {
        $this->width = $w;
        $this->height = $h;

        $this->lua = new \Lua();

        $this->lua->eval($startupScript);

        $this->magic = new \Imagick();
        $this->magic->newImage($w, $h, $color);

        // Создаем базовый слой для рисования
        $this->createLayer(true, 50, 1);

        $this->assignVar("_width", $w);
        $this->assignVar("_height", $h);


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
        $this->lua->registerCallback("newcolor", function ($name)
        {
            return new ImagickPixel($name);
        });
        $this->lua->registerCallback("print", function ($text)
        {
            $message = array();
            foreach (func_get_args() as $arg)
            {
                $message[] = $arg;
            }
            $this->dbgMessages[] = "print: " . implode(", ", $message);
        });

        $this->lua->registerCallback("array", function ()
        {
            return array(func_get_args());
        });

        $this->lua->registerCallback("dump", function ($data)
        {
            var_dump($data);
            $str = ob_get_clean();
            $this->dbgMessages[] = "dump: " . $str;
        });

        $this->addCallback("create_layer", array($this, 'createLayer'));
        $this->addCallback("clone_layer", array($this, 'cloneLayer'));
        $this->addCallback("change_layer_delay", array($this, 'changeLayerDelay'));
        $this->addCallback("use_layer", array($this, 'useLayer'));
        $this->addCallback("delete_layer", array($this, 'deleteLayer'));

    }

    public function createLayer($useCreated = false, $delay = 50, $id = null)
    {
        //var_dump(func_get_args());
        if ($id !== null && is_numeric($id))
        {
            $this->layers[$id] = new XImagickDraw($this);
            $this->layersDelays[$id] = $delay;
            if ($useCreated)
                $this->useLayer($id);
        } else
        {
            $this->layers[] = new XImagickDraw($this);
            $this->layersDelays[] = $delay;
            if ($useCreated)
                $this->useLayer(count($this->layers));
        }

        return count($this->layers);
    }

    public function cloneLayer($useCreated = false, $sourceLayer = null)
    {
        if ($sourceLayer === null)
        {
            $this->layers[] = clone $this->layers[count($this->layers)];
            $this->layersDelays[] = $this->layersDelays[count($this->layersDelays)];
            if($useCreated)
                $this->useLayer(count($this->layers));
        } else
        {
            $this->layers[] = clone $this->layers[$sourceLayer];
            $this->layersDelays[] = $this->layersDelays[$sourceLayer];
            if($useCreated)
                $this->useLayer($sourceLayer);
        }
        return count($this->layers);
    }

    public function changeLayerDelay($delay, $id=null)
    {
        if ($id === null)
        {
            $this->layersDelays[count($this->layersDelays)] = $delay;
        } else
        {
            $this->layersDelays[$id] = $delay;
        }
    }

    protected function bindActiveLayerCallbacks()
    {
        $imagickMethods = get_class_methods(get_class($this->layers[$this->activeLayer]));

        $disabledMethods = array();

        #var_dump($imagickMethods);
        foreach ($imagickMethods as $method)
        {
            $this->lua->registerCallback($method, array($this->layers[$this->activeLayer], $method));
        }

        $this->lua->registerCallback("getstrokecolor", function ()
        {
            return $this->layers[$this->activeLayer]->getStrokeColor()->getColor();
        });
        $this->lua->registerCallback("getfillcolor", function ()
        {
            return $this->layers[$this->activeLayer]->getFillColor()->getColor();
        });

        $this->lua->registerCallback("setstrokecolor", function ($value)
            {
                if (is_string($value))
                {
                    $this->layers[$this->activeLayer]->setStrokeColor($value);
                }
                if (is_array($value))
                {
                    $r = $value['r'];
                    $g = $value['g'];
                    $b = $value['b'];
                    $this->layers[$this->activeLayer]->setStrokeColor('#' . $this->rgbToHex($r, $g, $b));
                }
            }
        );

        $this->lua->registerCallback("setfillcolor", function ($value)
            {
                if (is_string($value))
                {
                    $this->layers[$this->activeLayer]->setFillColor($value);
                }
                if (is_array($value))
                {
                    $r = $value['r'];
                    $g = $value['g'];
                    $b = $value['b'];
                    $this->layers[$this->activeLayer]->setFillColor('#' . $this->rgbToHex($r, $g, $b));
                }
            }
        );
    }

    public function useLayer($id)
    {
        //echo("set active layer to ".$id);
        $this->activeLayer = $id;
        $this->bindActiveLayerCallbacks();
        $this->assignVar("_activeLayer", $id);
    }

    public function deleteLayer($id)
    {
        if (isset($this->layers[$id]))
        {
            unset($this->layers[$id]);
            if (isset($this->layersDelays[$id]))
                unset($this->layersDelays[$id]);
            $this->useLayer(count($this->layers));
            return true;
        }
        return false;
    }

    public function loadPlugins()
    {
        require_once "IPlugin.php";
        require_once "Plugin.php";

        foreach (glob(__DIR__ . "/plugins/*.php") as $pluginFile)
        {
            require_once $pluginFile;
            preg_match("#[^\/]+?$#", $pluginFile, $match);
            list($class_name, $ext) = explode(".", $match[0]);
            $class_name = '\DrawEngine\Plugins\\' . $class_name;
            $klass = new $class_name($this);
            $klass->init();
            $klass->load();
            $this->registerPlugin($class_name, $klass);
        }
    }

    public function unloadPlugins()
    {
        foreach ($this->plugins as $name => $instance)
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
        $this->useLayer(1);

        $fontSize = 12;
        $marginX = 2;
        $marginY = 2;
        $fontMetrics = $this->magic->queryFontMetrics($this->layers[$this->activeLayer], "test");

        $this->layers[$this->activeLayer]->setfontsize($fontSize);
        $this->layers[$this->activeLayer]->setfontfamily("Ubuntu");

        $this->layers[$this->activeLayer]->setfontweight(100);
        foreach ($this->dbgMessages as $id => $msg)
        {
            $this->layers[$this->activeLayer]->annotation($marginX, $fontSize * $id + $fontMetrics["characterHeight"] + $marginY, $msg);
        }
    }

    public function debug($message)
    {
        $this->dbgMessages[] = "debug: " . $message;
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
        foreach ($this->layers as $id => $layer)
        {
            if ($layer instanceof XImagickDraw)
            {
                $this->magic->drawImage($layer);
            }
        }
        $this->magic->setImageFormat("png");
        header('Content-Type: image/png');
        echo $this->magic;
    }

    public function renderAnim()
    {
        $this->afterDraw();
        $this->magic = new \Imagick();
        foreach ($this->layers as $id => $layer)
        {
            if ($layer instanceof XImagickDraw)
            {
                if ($id == 1)
                {
                    $this->magic->newImage($this->width, $this->height, "transparent");
                    $this->magic->drawImage($layer);
                    $this->magic->setimageDelay($this->layersDelays[$id]);
                    $this->magic->setImageFormat("gif");
                } else
                {
                    $frame = new \Imagick();
                    $frame->newImage($this->width, $this->height, "transparent");
                    $frame->drawImage($layer);
                    $frame->setimagedelay($this->layersDelays[$id]);
                    $frame->setImageFormat("gif");

                    $this->magic->addImage($frame);
                }
            }
        }
        //$this->magic = $this->magic->deconstructImages();
        $this->magic->writeimages("/tmp/gif.gif", true);
        header('Content-Type: image/gif');
        echo $this->magic->getImagesBlob();
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