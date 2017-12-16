<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../generated-conf/config.php';
$config = require __DIR__ . '/../config.php';

$app = new \Slim\App(['settings' => $config]);

$app->get('/about', function(Request $request, Response $response) {
	return $response->withJson(['version' => '0.0.1']);
});

\Gtd\SessionInit::init($app);
\Gtd\Routes\Auth::init($app);

$app->run();