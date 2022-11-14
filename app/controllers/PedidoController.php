<?php
require_once './models/Pedido.php';
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(!empty($parametros['nombreCliente']) && !empty($parametros['codigoDeLaMesaUtilizada']))
        {
          $codigoDeMesa = $parametros['codigoDeLaMesaUtilizada'];
          $seEncontroMesa=Mesa::obtenerMesa($codigoDeMesa);
          if($seEncontroMesa)
          {
            $numeroDePedido=PedidoController::crearNroDePedido();
            
            $pedido = new Pedido();
            $pedido->nombreDelCliente = $parametros['nombreCliente'];
            $pedido->nroDePedido = $numeroDePedido;
            $pedido->codigoDeMesaAsociada = $codigoDeMesa;
            $pedido->pathFoto = PedidoController::generarPathImagen($request, $numeroDePedido);
            $pedido->fechaDeCuandoSeTomoElPedido = date('Y/m/d H:i:s');
            $pedido->crearPedido();
    
            $payload = json_encode(array("mensaje" => "Pedido creado con exito: El numero de pedido es: ${numeroDePedido} y el codigo de su mesa es: ${codigoDeMesa}"));
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
}
