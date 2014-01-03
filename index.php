<?

#error_reporting(E_ALL ^ E_WARNING);


require_once "DrawEngine.php";


function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
    // error was suppressed with the @-operator
    if (0 === error_reporting())
    {
        return false;
    }

    throw new Exception($errstr, $errno);
}

set_error_handler('handleError');

$start = microtime(true);

$engine = null;

try
{
    $engine = new \DrawEngine\DrawEngine(file_get_contents("script.lua"), 320, 150, "green");
    $engine->registerMethods();
    $engine->loadPlugins();
    $engine->run("frame1", array());
} catch (Exception $e)
{
    echo $e->getMessage();
    echo "<br />" . $e->getFile() . ":" . $e->getLine();
    return 1;
}
$engine->debug("generated in: ".number_format(((microtime(true)-$start)), 3, '.', ' ')." sec");

$engine->render();