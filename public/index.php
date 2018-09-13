<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../config/db.php';


$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;


// Create app
$app = new \Slim\App(['settings' => $config]);

// Get container
$container = $app->getContainer();

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('../templates', [
        'cache' => '../cache'
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));

    return $view;
};

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
    $response = $this->view->render($response, "defensorias.phtml", ["defensorias" => $defensorias]);//, "router" => $this->router]);
    //$response->getBody()->write(var_export($defensorias, true));
    return $response;
});

$app->get('/hello/{name}', function ($request, $response, $args) {
    $this->logger->addInfo("algo para el log");
    return $this->view->render($response, 'profile.html', [
        'name' => $args['name']
    ]);
})->setName('profile');

// Run app
$app->run();



?>