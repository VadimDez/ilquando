<?php

// root url
$baseUrl = "/ilquando";

// model
require_once('src/Model/engine.php');

// Carica tutte le classi necessarie a Tonic
require_once 'src/Tonic/Autoloader.php';

/** INIZIO DOCTRINE DBAL **/
use Doctrine\Common\ClassLoader;

/* Doctrine uses a class loader to autoload the required classes */
require_once 'src/Doctrine/Common/ClassLoader.php';

/* Lets first load the Doctrine DBAL lbrary */
$loader = new \Doctrine\Common\ClassLoader("Doctrine", __DIR__."/src");
$loader->register();

use Doctrine\DBAL\DriverManager;


$dbParams = array(
    'driver' => 'mysqli',
    'host'    => 'localhost',
    'user' => 'root',
    'password' => '',
    'dbname' => 'ilquando'
);
$config = new \Doctrine\DBAL\Configuration();
$conn = DriverManager::getConnection($dbParams, $config);
/** FINE DOCTRINE DBAL **/


// Twig
require_once 'src/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('src/Views');
$twig = new Twig_Environment($loader);


// Carico I18n
$locale = "";

// verifico se si vuole forzare la lingua
if ( isset($_REQUEST["locale"]) )
{

	$locale = $_REQUEST["locale"];

}
else
{
	$headers = getallheaders();

	if ( array_key_exists("Accept-Language", $headers) )
    {
		// 	utilizzo l'http Accept-Language header per intercettare la lingua
		$header = $headers["Accept-Language"];

		if ( strlen($header) >= 2) {
			$locale = substr($header, 0, 2);
		}
	}
}

// costruisco il nome del file
$i18nfile = "src/i18n/i18n_" . $locale . ".json";
$i18n = json_decode("{}");

// verifico se il file esiste
if ( file_exists($i18nfile) )
{
	$i18njson = file_get_contents($i18nfile);
}
else
{
	// se non trovo nessun file di default carico quello italiano
	$i18njson = file_get_contents("src/i18n/i18n_it.json");
}

//decodifico il json
$i18n = json_decode($i18njson);

// aggiungo l'i18n per tutti i template
$twig->addGlobal("i18n", $i18n);
$twig->addGlobal("burl", $baseUrl);

// container disponibile da tutti i controllers
$container = array(
		"twig"  => $twig,
		"burl"  => $baseUrl,
        "conn"  => $conn
);

// Tonic options
$opt = array(
		'load' => 'src/Resources/*.php'
);

// Inizializzo l'applicazione indicando dove sono le risorse
$app = new Tonic\Application($opt);

// recupero la request HTTP
$request = new Tonic\Request();

//decode JSON data received from HTTP request
if ($request->contentType == 'application/json')
{
	$request->data = json_decode($request->data);
}

// gestisco l'errore nel caso la risorsa non esista
try{
	$resource = $app->getResource($request);
} catch(Tonic\NotFoundException $e) {
	$resource = new Resources\NotFoundResource($app, $request, array());
}

$resource->container = $container;

// gestisco l'errore nel caso esploda il mio controller
try {
	$response = $resource->exec();
} catch(Tonic\Exception $e) {
	$resource = new Resources\FatalErrorResource($app, $request, array());
	$response = $resource->exec();
}

// encode output
if ($response->contentType == 'application/json') {
	$response->body = json_encode($response->body);
}

$response->output();
?>