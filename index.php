<?

#error_reporting(E_ALL ^ E_WARNING);
require "lib.php";

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
    if(!isset($_REQUEST['user']))
        die("No user specified!");

    $user = trim(str_replace("\0", "", $_REQUEST['user']));

    if(!file_exists("users/".$user.".lua"))
        die("Userscript was not found!");

    $engine = new \DrawEngine\DrawEngine(file_get_contents("users/".$user.".lua"), 350, 40, "silver");
    //$engine->debugMode = true;
    $engine->registerMethods();
    $engine->loadPlugins();
    $engine->run("draw", array());
} catch (Exception $e)
{
    echo $e->getMessage();
    echo "<br />" . $e->getFile() . ":" . $e->getLine();
    return 1;
}
$engine->debug("generated in: " . number_format(((microtime(true) - $start)), 3, '.', ' ') . " sec");

if (isset($_REQUEST['mode']))
{
    switch ($_REQUEST['mode'])
    {
        case "gif":
            $engine->renderAnim();
            break;
        case "png":
            $engine->render();
            break;
    }
} else
{
    $engine->render();
}