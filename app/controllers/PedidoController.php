<?php

use GuzzleHttp\Psr7\Message;

require_once './models/Pedido.php';
require_once './models/Mesa.php';
require_once './models/Empleado.php';
require_once './models/Encuesta.php';
require_once './models/ordenProducto.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido
{
    public function CargarUno($request, $response, $args)
    {
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
              $pedido->idMozoEncargado = PedidoController::conseguirIdDelMozoQueTomoElPedidoPorElToken($request);
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

    public function realizarEncuesta($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $payload = json_encode(array("ListaPedidos" => "HACER ENCUESTA"));

      //verifico que no me envien ningun dato vacio
      if(!empty($parametros['puntajeMesa']) && !empty($parametros['puntajeResto']) && !empty($parametros['puntajeMozo']) 
      && !empty($parametros['puntajeCocinero']) && !empty($parametros['descripcion'] && !empty($parametros['codigoDeLaMesaUtilizada']) && !empty($parametros['numPedido'])))
      {
        //si no hay ningun dato vacio traigo la mesa y el pedido que corresponden a los codigos que me mandan
        $mesaEncontrada = Mesa::obtenerMesa($parametros['codigoDeLaMesaUtilizada']);
        $pedidoEncontrado = Pedido::obtenerPedido($parametros['numPedido']);
        //chekeo que la mesa y el pedido exista
        if($mesaEncontrada && $pedidoEncontrado)
        {
          //si existen chequeo que se pueda realizar la encuesta
          if($pedidoEncontrado->estado == "entregado" && $pedidoEncontrado->codigoDeMesaAsociada == $parametros['codigoDeLaMesaUtilizada'])
          {
            //si se puede realizar la encuesta valido que los datos que me pasen sean validos
            $descripcion = $parametros['descripcion'];
            if(strlen($descripcion)<66)
            {
              $puntajeMesa = floatval($parametros['puntajeMesa']);
              $puntajeResto = floatval($parametros['puntajeResto']);
              $puntajeMozo = floatval($parametros['puntajeMozo']);
              $puntajeCocinero = floatval($parametros['puntajeCocinero']);
              if(($puntajeMesa<=10 && $puntajeMesa>0) && ($puntajeResto<=10 && $puntajeResto>0) && ($puntajeMozo<=10 && $puntajeMozo>0) && ($puntajeCocinero<=10 && $puntajeCocinero>0))
              {
                $promedio=($puntajeMesa+$puntajeResto+$puntajeMozo+$puntajeCocinero)/4;
                Encuesta::crearEncuensta($puntajeMesa, $puntajeResto, $puntajeMozo, $puntajeCocinero, $promedio, $descripcion, $pedidoEncontrado->nroDePedido, $pedidoEncontrado->codigoDeMesaAsociada);
                $payload = json_encode(array("mensaje" => "La encuesta se realizo con exito!"));
              }
              else
              {
                $payload = json_encode(array("Error" => "Los puntajes deben ser del 1 al 10"));
              }
            }
            else
            {
              $payload = json_encode(array("Error" => "La descripcion como maximo puede ser de 66 caracteres"));
            }
          }
          else
          {
            $payload = json_encode(array("Error" => "El codigo de mesa enviado no corresponde a la mesa que se utilizo en el pedido o el pedido todavia no fue entregado"));
          }
        }
        else
        {
          $payload = json_encode(array("Error" => "La mesa o el pedido enviado no existen"));
        }
      }
      else
      {
        $payload = json_encode(array("Error" => "Error en los parametros enviados. Verifique los parametros por favor"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function mejoresComentariosEncuesta($request, $response, $args)
    {
      $payload=json_encode(array("MejoresComentarios" => Encuesta::obtenerMejoresComentarios()));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function conseguirIdDelMozoQueTomoElPedidoPorElToken($request)
    {
      $header = $request->getHeaderLine('authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $mozoQueTomoElPedido=Empleado::obtenerEmpleado(AutentificadorJWT::ObtenerData($token)->nickNameUser);
      return $mozoQueTomoElPedido->id;
    }

    
}
