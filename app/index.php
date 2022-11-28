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
require_once './controllers/OrdenarProductoController.php';
require_once './middlewares/MwVerificacionDeAccesos.php';


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
  $group->get('/traer/pedidosListosParaServir', \EmpleadoController::class . ':traerPedidosListosParaServir')
  ->add(\MwVerificacionDeAccesos::class . ':esMozo');
  $group->put('/{nroPedido}', \EmpleadoController::class . ':entregarPedidoYCambiarEstadoMesa')
  ->add(\MwVerificacionDeAccesos::class . ':esMozo');
  $group->post('/AltaEmpleado', \EmpleadoController::class . ':CargarUno');
  $group->post('/mozo/cobrarCuenta', \EmpleadoController::class . ':cobrarCuenta')
  ->add(\MwVerificacionDeAccesos::class . ':esMozo');
  $group->post('[/]', \EmpleadoController::class . ':loginEmpleado');
  $group->delete('/cerrarMesa/{codigoDeMesa}', \EmpleadoController::class . ':cerrarMesa')
  ->add(\MwVerificacionDeAccesos::class . ':esSocio');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/{numPedido}', \PedidoController::class . ':TraerUno');
  $group->get('/obtener/tiempoDeDemora', \MesaController::class . ':obtenerTiempoDeEspera');
  $group->post('/tomarPedido', \PedidoController::class . ':CargarUno')
  ->add(\MwVerificacionDeAccesos::class . ':esMozo');
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{codigoMesa}', \MesaController::class . ':TraerUno');
  $group->get('/mesa/MasUtilizada', \MesaController::class . ':traerMesaMasUtilizada');
  $group->post('/altaMesa', \MesaController::class . ':CargarUno');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{idProdu}', \ProductoController::class . ':TraerUno');
  $group->post('/csv/crearCsvConDatosDeUnaTabla', \ProductoController::class . ':crearCsvConDatosDeUnaTabla');
  $group->post('/csv/cargar', \ProductoController::class . ':cargarTablaConDatosDelCSv');
  $group->post('/altaProducto', \ProductoController::class . ':CargarUno');
});

$app->group('/ordenes', function (RouteCollectorProxy $group) {
  $group->get('[/]', \OrdenarProductoController::class . ':TraerTodos');
  $group->get('/{idorden}', \OrdenarProductoController::class . ':TraerUno');
  $group->get('/traerPendientes/{nroPedido}', \OrdenarProductoController::class . ':TraerPendientes')
  ->add(\MwVerificacionDeAccesos::class . ':esEmpleado');
  $group->get('/traer/enPreparacion/{nroPedido}', \OrdenarProductoController::class . ':TraerOrdenesEnPreparacion')
  ->add(\MwVerificacionDeAccesos::class . ':esEmpleado');
  $group->put('/{nroPedido}', \OrdenarProductoController::class . ':cambiarEstadoEnPreparacionYAsignarTimpo')
  ->add(\MwVerificacionDeAccesos::class . ':esEmpleado');
  $group->put('/listaParaServir/{nroPedido}', \OrdenarProductoController::class . ':cambiarEstadoEnListoParaServir')
  ->add(\MwVerificacionDeAccesos::class . ':esEmpleado');
  $group->post('/ordenarProducto', \OrdenarProductoController::class . ':CargarUno')
  ->add(\MwVerificacionDeAccesos::class . ':esMozo');
});

$app->group('/encuesta', function (RouteCollectorProxy $group) {
  $group->get('/mejoresComentarios', \PedidoController::class . ':mejoresComentariosEncuesta');
  $group->post('[/]', \PedidoController::class . ':realizarEncuesta');
});

$app->run();
