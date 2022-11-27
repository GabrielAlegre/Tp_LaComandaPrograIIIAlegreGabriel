<?php
require_once './models/Pedido.php';
require_once './models/Mesa.php';
require_once './models/ordenProducto.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido
{
    public function CargarUno($request, $response, $args)
    {
      echo "hola";
        $parametros = $request->getParsedBody();

        if(!empty($parametros['nombreCliente']) && !empty($parametros['codigoDeLaMesaUtilizada']))
        {
          $codigoDeMesa = $parametros['codigoDeLaMesaUtilizada'];
          $mesaEncontrada=Mesa::obtenerMesa($codigoDeMesa);
          if($mesaEncontrada)
          {
            if($mesaEncontrada->estado==="cerrada")
            {
              Mesa::actualizarEstadoMesa($codigoDeMesa, "con cliente esperando al mozo para ordenar productos");
              $numeroDePedido=PedidoController::crearNroDePedido();
              $pedido = new Pedido();
              $pedido->nombreDelCliente = $parametros['nombreCliente'];
              $pedido->nroDePedido = $numeroDePedido;
              $pedido->codigoDeMesaAsociada = $codigoDeMesa;
              $pedido->pathFoto = PedidoController::generarPathImagen($request, $numeroDePedido);
              $pedido->crearPedido();
      
              $payload = json_encode(array("mensaje" => "Pedido creado con exito! El numero de pedido es: ${numeroDePedido} y el codigo de su mesa es: ${codigoDeMesa}, estamos a la espera de que ordene los productos que quiera!"));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "Lo sentimos, la mesa con el codigo ${codigoDeMesa} esta en uso"));
            }
          }
          else
          {
            $payload = json_encode(array("mensaje" => "No existe la mesa con el codigo: ${codigoDeMesa}"));
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
        // Buscamos empreado por nombre
        $nroDePedido = $args['numPedido'];
        $pedido = Pedido::obtenerPedido($nroDePedido);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("ListaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function crearNroDePedido()
    {
      $caracteresPermitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      return substr(str_shuffle($caracteresPermitidos), 0, 5);
    }

    public static function generarPathImagen($request, $numeroDePedido)
    {
      $foto = $request->getUploadedFiles()['foto'];
      if ($foto->getError() === UPLOAD_ERR_OK) {
        //ok foto
        $nombreDeLaImg = $numeroDePedido.'.jpg';
        if (!file_exists('../app/archivos/fotosPedido/'))
        {
            mkdir('../app/archivos/fotosPedido/', 0777, true);
        }
        $path = "../app/archivos/fotosPedido/".$nombreDeLaImg;
        $foto->moveTo($path);
      }else{
        //no subio foto o error
        $path = "";
      }
      return $path;
    }

    public static function cambiarEstadoAlPedidoEnListoParaServir($nroDePedido)
    {
      $sePuedeCambiarEstado = true;
      $ordenesDelPedido = ordenProducto::traerTodasLasOrdenesDeUnPedido($nroDePedido);
      foreach ($ordenesDelPedido as $unaOrdenDelPedido) {
        if($unaOrdenDelPedido->estado!="listo para servir"){
          $sePuedeCambiarEstado = false;
          break;
        }
      }
      return $sePuedeCambiarEstado?PedidoController::actualizarEstadoPedido($nroDePedido, "listo para servir"):false;
    }
}
