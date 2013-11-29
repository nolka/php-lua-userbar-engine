<?

error_reporting(E_ALL ^E_WARNING);

require_once "DrawEngine.php";


function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new Exception($errstr,  $errno);
}

#set_error_handler('handleError');


  $start = microtime(true);

  $engine = new DrawEngine(file_get_contents("script.lua"), 400,400, "green");
  $engine->registerMethods();
  
  $xml = new SimpleXMLElement("http://api.twitter.com/1/statuses/user_timeline.rss?screen_name=nolka4", null, true);

  
  $engine->run("main", array($xml));
  #$engine->draw->annotation(5,15, "generated in: ".number_format(((microtime(true)-$start)), 3, '.', ' ')." sec");
  
  $engine->render();