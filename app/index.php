<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/EmpleadoController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->group('/empleados', function (RouteCollectorProxy $group) {
  $group->get('[/]', \EmpleadoController::class . ':TraerTodos');
  $group->get('/{nick}', \EmpleadoController::class . ':TraerUno');
  $group->post('/AltaEmpleado', \EmpleadoController::class . ':CargarUno');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/{numPedido}', \PedidoController::class . ':TraerUno');
  $group->post('/tomarPedido', \PedidoController::class . ':CargarUno');
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{codigoMesa}', \MesaController::class . ':TraerUno');
  $group->post('/altaMesa', \MesaController::class . ':CargarUno');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{idProdu}', \ProductoController::class . ':TraerUno');
  $group->post('/altaProducto', \ProductoController::class . ':CargarUno');
});

$app->run();