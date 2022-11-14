<?php
require_once './models/Producto.php';
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      if(!empty($parametros['nombre']) && !empty($parametros['cantidad']) && !empty($parametros['precio']) && !empty($parametros['tiempoDePreparacion']) && !empty($parametros['sectorEncargado']) && !empty($parametros['numeroDePedido']))
      {
        $nroDePedido=$parametros['numeroDePedido'];
        $seEncontroPedido=Pedido::obtenerPedido($nroDePedido);
        if($seEncontroPedido)
        {
          $producto = new Producto();
          $producto->nombre = $parametros['nombre'];
          $producto->cantidad = $parametros['cantidad'];
          $producto->precio = $parametros['precio'];
          $producto->tiempoDePreparacion = $parametros['tiempoDePreparacion'];
          $producto->sectorEncargado = $parametros['sectorEncargado'];
          $producto->numeroDePedido = $nroDePedido;
          $producto->estado="Pendiente";
          $producto->crearProducto();
          $payload = json_encode(array("mensaje" => "Se ordeno con exito el producto corresponde al pedido: ".$producto->numeroDePedido));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No existe un pedido con el numero de pedido: ${nroDePedido}"));
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
        // Buscamos mesa por su codigo indificador
        $id = $args['idProdu'];
        $produ = Producto::obtenerProducto($id);
        $payload = json_encode($produ);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("Lista de productos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
