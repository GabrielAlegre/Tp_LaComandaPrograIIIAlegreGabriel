<?php

use GuzzleHttp\Psr7\Message;

require_once './models/ordenProducto.php';
require_once './controllers/PedidoController.php';
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class OrdenarProductoController extends Producto
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    if(!empty($parametros['idDelProductoElegido']) && !empty($parametros['nroDePedidoAlQueCorrespondeLaOrden']))
    {
      $nroDePedidoRelacionado = $parametros['nroDePedidoAlQueCorrespondeLaOrden'];
      $idDelProductoOrdenado = $parametros['idDelProductoElegido'];
      $pedidoAlQueCorrespondeLaOrden = Pedido::obtenerPedido($nroDePedidoRelacionado);
      $productoQueSeOrdenara = Producto::obtenerProducto($idDelProductoOrdenado);

      if($pedidoAlQueCorrespondeLaOrden)
      {
        if($productoQueSeOrdenara)
        {
          if($pedidoAlQueCorrespondeLaOrden->estado == "Esperando que el cliente ordene")
          Pedido::actualizarEstadoPedido($pedidoAlQueCorrespondeLaOrden->nroDePedido, "Pendiente");
          Mesa::actualizarEstadoMesa($pedidoAlQueCorrespondeLaOrden->codigoDeMesaAsociada, "con cliente esperando pedido");
          $orden = new ordenProducto();
          $orden->idDelProductoElegido = $idDelProductoOrdenado;
          $orden->nroDePedidoAlQueCorrespondeLaOrden = $nroDePedidoRelacionado;
          $orden->estado = "pendiente";
          $orden->crearUnaOrdenDeProducto();
          Pedido::asignarlePrecioTotalAlPedido($pedidoAlQueCorrespondeLaOrden->nroDePedido, Pedido::obtenerPrecioTotalDelPedido($nroDePedidoRelacionado));
          $payload = json_encode(array("mensaje" => "Se ordeno con exito el producto '{$productoQueSeOrdenara->nombre}' correspondiente al pedido: ".$orden->nroDePedidoAlQueCorrespondeLaOrden));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Disculpe, actualmente no existe ningun producto con el id: ${idDelProductoOrdenado}"));
        }
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No existe ningun pedido con el numero de pedido: ${nroDePedidoRelacionado}"));
      }
    }
    else
    {
      $payload = json_encode(array("mensaje" => "Error en los parametros enviados. Verifique los parametros por favor"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
      $id = $args['idOrden'];
      $produ = ordenProducto::obtenerOrden($id);
      $payload = json_encode($produ);

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
      $lista = ordenProducto::obtenerTodos();
      $payload = json_encode(array("Ordenes" => $lista));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPendientes($request, $response, $args)
  {
      $numPedido = $args['nroPedido'];
      $sector = OrdenarProductoController::conseguirDatosDelEmpleadoPorElToken($request, "sector");
      $lista = ordenProducto::obtenerOrdenesPorSuEstadoAndSector($numPedido, $sector, "pendiente");
      $payload = !empty($lista)?json_encode(array("OrdenesPendientes" => $lista)):json_encode(array("OrdenesPendientes" => "No se ordeno ningun producto en el pedido '{$numPedido}' correspondiente al sector '{$sector}'"));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerOrdenesEnPreparacion($request, $response, $args)
  {
      $numPedido = $args['nroPedido'];
      $sector = OrdenarProductoController::conseguirDatosDelEmpleadoPorElToken($request, "sector");
      $lista = ordenProducto::obtenerOrdenesPorSuEstadoAndSector($numPedido, $sector, "en preparacion");
      $payload = !empty($lista)?json_encode(array("OrdenesEnPreparacion" => $lista)):json_encode(array("OrdenesEnPreparacion" => "No se ordeno ningun producto en el pedido '{$numPedido}' correspondiente al sector '{$sector}'"));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function cambiarEstadoEnPreparacionYAsignarTimpo($request, $response, $args)
  {
      $numPedido = $args['nroPedido'];
      $sector = OrdenarProductoController::conseguirDatosDelEmpleadoPorElToken($request, "sector");
      $idDelEmpleadoQuePrepararaLaOrden = OrdenarProductoController::conseguirDatosDelEmpleadoPorElToken($request, "id");
      $tiempoEstimadoDePreparacion = $sector=="cocinero"?rand(15,55):rand(3,8);
      $seCambioEstado = ordenProducto::actualizarEstadoOrdenAndTiempoPreparacionPorSector($numPedido, "en preparacion", $tiempoEstimadoDePreparacion, $sector, $idDelEmpleadoQuePrepararaLaOrden);
      if($seCambioEstado)
      {
        Pedido::actualizarEstadoPedido($numPedido, "en preparacion");
        Pedido::asignarleTiempoDePreparacionEstimado($numPedido, Mesa::obtenerTiempoEspera($numPedido));
        $payload = json_encode(array("mensaje" => "Una orden del pedido '{$numPedido}' del sector '{$sector}' se puso en preparacion"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se ordeno ningun producto en el pedido '{$numPedido}' correspondiente al sector '{$sector}' o la orden esta o paso por la preparacion"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function cambiarEstadoEnListoParaServir($request, $response, $args)
  {
      $numPedido = $args['nroPedido'];
      $sector = OrdenarProductoController::conseguirDatosDelEmpleadoPorElToken($request, "sector");
      $seCambioEstado = ordenProducto::actualizarEstadoEnListoParaServir($numPedido, "listo para servir", $sector);
      echo $seCambioEstado;
      $pedidoCambioEstado = PedidoController::cambiarEstadoAlPedidoEnListoParaServir($numPedido);

      if($pedidoCambioEstado)
      {
        $payload = json_encode(array("mensaje" => "Orden del pedido '{$numPedido}' del sector '{$sector}' esta lista para servir, con esta ultima todas las ordenes del pedido estan listos para servir, por ende el pedido tambien esta listo para servir!"));
      }
      else
      {
        $payload = $seCambioEstado?json_encode(array("mensaje" => "Una orden del pedido '{$numPedido}' del sector '{$sector}' esta listo para servir")):
        json_encode(array("mensaje" => "No se ordeno ningun producto en el pedido '{$numPedido}' correspondiente al sector '{$sector}'"));
      }
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public static function conseguirDatosDelEmpleadoPorElToken($request, $queDatoConseguir)
  {
    $header = $request->getHeaderLine('authorization');
    $token = trim(explode("Bearer", $header)[1]);
    if($queDatoConseguir=="sector")
    {
      return AutentificadorJWT::ObtenerData($token)->tipo;
    }
    else if($queDatoConseguir=="id")
    {
      $empleadoQuePreparaLaOrden=Empleado::obtenerEmpleado(AutentificadorJWT::ObtenerData($token)->nickNameUser);
      return $empleadoQuePreparaLaOrden->id;
    }
  }
}
