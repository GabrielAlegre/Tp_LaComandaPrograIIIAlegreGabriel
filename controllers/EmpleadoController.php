<?php
require_once './models/Empleado.php';
require_once './models/Pedido.php';
require_once './models/Mesa.php';
require_once './models/ordenProducto.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';

class EmpleadoController extends Empleado
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(!empty($parametros['nombre']) && !empty($parametros['nickUser']) && !empty($parametros['clave']) && !empty($parametros['tipoDeEmpleado']))
        {
          $personalPermitido = array("bartender", "cervecero", "cocinero", "mozo", "socio");
          $perfilEmpleado = strtolower($parametros['tipoDeEmpleado']);
          if(in_array($perfilEmpleado, $personalPermitido))
          {
            $empleado = new Empleado();
            $empleado->nombre =  strtolower($parametros['nombre']);
            $empleado->nickNameUser = strtolower($parametros['nickUser']);
            $empleado->clave = $parametros['clave'];
            $empleado->tipoDeEmpleado = $perfilEmpleado;
            $empleado->crearEmpleado();
            $payload = json_encode(array("Mensaje" => "Empleado creado con exitooo"));
          }
          else{
            $payload = json_encode(array("Error" => "No se pudo realizar la alta del empleado. Los tipos de personal disponible son: 'bartender' - 'cervecero' - 'cocinero' - 'mozo' - 'socio'"));
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

    public function TraerUno($request, $response, $args)
    {
        // Buscamos empleado por su nick
        $nickNameEmpleado = $args['nick'];
        $empleado = Empleado::obtenerEmpleado($nickNameEmpleado);
        $payload = json_encode($empleado);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Empleado::obtenerTodos();
        $payload = json_encode(array("ListaEmpleados" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function traerPedidosListosParaServir($request, $response, $args)
    {
        $listaDePedidosListosParaServir = Pedido::obtenerTodosLoPedidosQueEstanListosParaServir();
        $payload = !empty($listaDePedidosListosParaServir)?json_encode(array("PedidosListosParaServir" => $listaDePedidosListosParaServir)):json_encode(array("PedidosListosParaServir" => "No hay ningun pedido listo para servir"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function entregarPedidoYCambiarEstadoMesa($request, $response, $args)
    {
      $numPedido = $args['nroPedido'];
      $pedidoQueSeEntregara = Pedido::obtenerPedido($numPedido);

      if($pedidoQueSeEntregara)
      {
        if($pedidoQueSeEntregara->estado=="listo para servir"){
          Mesa::actualizarEstadoMesa($pedidoQueSeEntregara->codigoDeMesaAsociada, "con cliente comiendo");
          ordenProducto::actualizarEstadoOrdenes($numPedido, "entregado");
          Pedido::actualizarEstadoPedido($numPedido, "entregado");
          $payload = json_encode(array("mensaje" => "El pedido '{$numPedido}' fue entregado con exito! Los clientes ya estan comiendo"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "El pedido '{$numPedido}' no esta listo para servir"));
        }
      }
      else
      {
        $payload = json_encode(array("mensaje" => "El pedido '{$numPedido}' no existe"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function cobrarCuenta($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      if(!empty($parametros['numeroDePedido']))
      {
        $numPedido = $parametros['numeroDePedido'];
        $pedidoQueSeCobrara=Pedido::obtenerPedido($numPedido);
        if($pedidoQueSeCobrara)
        {
          $seModifico=Mesa::cobrarMesaYCambiarEstado($pedidoQueSeCobrara->codigoDeMesaAsociada, "con cliente pagando");
          $payload = $seModifico?
          json_encode(array("mensaje" => "Se cobro exitosamente, el monto que se pago fue de {$pedidoQueSeCobrara->precioTotal} pesos! Le recordamos que su pedido fue el: ${numPedido} y el codigo de la mesa es: {$pedidoQueSeCobrara->codigoDeMesaAsociada}, para realizar la encuesta"))
          :json_encode(array("mensaje" => "Solo se puede cobrar el pedido cuando este ya fue terminado y entregado"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "El pedido '{$numPedido}' no existe"));
        }
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function cerrarMesa($request, $response, $args)
    {
      $codigoMesa = $args['codigoDeMesa'];
      $mesaQueSeCerrara=Mesa::obtenerMesa($codigoMesa);

      if($mesaQueSeCerrara)
      {
        $seCerro=Mesa::cerrarMesa($codigoMesa);
        $payload = $seCerro?json_encode(array("mensaje" => "La mesa '{$codigoMesa}' fue cerrada exitosamente!")):json_encode(array("mensaje" => "Solo se puede cerra la mesa cuando el cliente este pagando"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "La mesa '{$codigoMesa}' no existe"));
      }
 

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function loginEmpleado($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      if(!empty($parametros['nickNameUser']) && !empty($parametros['clave']))
      {
          $parametros = $request->getParsedBody();
          $claveIngresada=$parametros['clave'];
          $nickNameUserIngresado=$parametros['nickNameUser'];

          $empleadoEncontrado=Empleado::obtenerEmpleado($nickNameUserIngresado);

          if($empleadoEncontrado)
          {
            if(password_verify($claveIngresada, $empleadoEncontrado->clave))
            {
              $tipoDeEmpleado=$empleadoEncontrado->tipoDeEmpleado;
              $datos = array('nickNameUser' => $nickNameUserIngresado, 'tipo' => $empleadoEncontrado->tipoDeEmpleado);
              $token = AutentificadorJWT::CrearToken($datos);

              $payload = json_encode(array( "Informacion" => "OK. Tipo de perfil del empleado:  ${tipoDeEmpleado}", "Token" => $token));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "Contraseña ingresada incorrecta"));
            }
          }
          else
          {
            $payload = json_encode(array("mensaje" => "No existe ningun empleado con el nickNameUser ${nickNameUserIngresado}"));
          }
      }
      else
      {
        $payload = json_encode(array("mensaje" => "Error en los parametros enviados. Verifique ninguno este vacio por favor"));
      }

      $response->getBody()->write($payload);
      return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
