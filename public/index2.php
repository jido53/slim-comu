<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../config/db.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;





//$app = new \Slim\App;
$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('pgsql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['view'] = new \Slim\Views\PhpRenderer('../templates/');

$app->get('/defensorias', function (Request $request, Response $response) {
    $this->logger->addInfo("Listado de Defensorias");
    $mapper = new DefensoriaMapper($this->db);
    $defensorias = $mapper->getDefensorias();
    //$response = $this->view->render($response, "defensorias.phtml", ["defensorias" => $defensorias]);//, "router" => $this->router]);
    $response->getBody()->write(var_export($defensorias, true));
    return $response;
});




$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    $this->logger->addInfo('Something interesting happened');

    return $response;
});
$app->run();
