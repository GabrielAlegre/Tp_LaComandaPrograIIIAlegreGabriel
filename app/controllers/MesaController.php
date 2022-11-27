<?php

require_once './models/Mesa.php';
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa
{
    public function CargarUno($request, $response, $args)
    {
        $codigo=MesaController::crearCodigoMesa();
        $mesa = new Mesa();
        $mesa->codigoDeMesa = $codigo;
        $mesa->estado = "cerrada";
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito: El codigo de mesa es: ${codigo}"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos mesa por su codigo indificador
        $codigo = $args['codigoMesa'];
        $mesa = Mesa::obtenerMesa($codigo);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("Lista de mesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function crearCodigoMesa()
    {
      $caracteresPermitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      return substr(str_shuffle($caracteresPermitidos), 0, 5);
    }

    public static function traerMesaMasUtilizada($request, $response, $args)
    {
      $mesasUtilizadas=array_map( function($a){return $a->codigoDeMesaAsociada;}, Pedido::obtenerTodos());
      $mesasConLasVecesQueLasUtilizaron=array_count_values($mesasUtilizadas);

      $mesasMasUtilizadas=array_keys($mesasConLasVecesQueLasUtilizaron, max($mesasConLasVecesQueLasUtilizaron));

      $payload = json_encode(array("mesasMasUtilizadas" => $mesasMasUtilizadas));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

    public static function obtenerTiempoDeEspera($request, $response, $args)
    {
      if(!empty($_GET["codigoDeMesa"]) && !empty($_GET["numeroDePedido"]))
      {
        $nroPedio=$_GET["numeroDePedido"];
        $codigoMesa=$_GET["codigoDeMesa"];
        $pedido=Pedido::obtenerPedido($nroPedio);
        if($pedido)
        {
          if($pedido->codigoDeMesaAsociada == $codigoMesa)
          {
            $minutosDemora=Mesa::obtenerTiempoEspera($nroPedio);
            $payload = $minutosDemora?json_encode(array("mensaje" => "La demora del pedido '{$nroPedio}' es de {$minutosDemora} minutos")):json_encode(array("mensaje" => "Su pedido ya esta listo para entregar o ya fue entregado"));
          }
          else
          {
            $payload = json_encode(array("Error" => "La mesa '{$codigoMesa}' no es la que se utiliza en el pedido '{$nroPedio}', verifique los datos por favor"));
          }
        }
        else
        {
          $payload = json_encode(array("Error" => "No existe ningun pedido con el nro de pedido '{$nroPedio}'"));
        }
      }
      else
      {
        $payload = json_encode(array("Error" => "Error en los parametros enviados. Verifique que ninguno haya quedado vacio por favor"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }
}
