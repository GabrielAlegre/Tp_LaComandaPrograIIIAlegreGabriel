<?php
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';

class EmpleadoController extends Empleado
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(!empty($parametros['nombre']) && !empty($parametros['nickUser']) && !empty($parametros['clave']) && !empty($parametros['TipoDePersonal']))
        {
          $personalPermitido = array("bartender", "cervecero", "cocinero", "mozo", "socio");
          $tipoPersonal = strtolower($parametros['TipoDePersonal']);
          if(in_array($tipoPersonal, $personalPermitido))
          {
            $empleado = new Empleado();
            $empleado->nombre =  strtolower($parametros['nombre']);
            $empleado->nickNameUser = strtolower($parametros['nickUser']);
            $empleado->clave = $parametros['clave'];
            $empleado->tipoDePersonal = $tipoPersonal;
            $empleado->crearEmpleado();
            $payload = json_encode(array("Mensaje" => "Empleado creado con exitooo"));
          }
          else{
            $payload = json_encode(array("Error" => "No se pudo realizar la alta del empleado. Los tipos de personal disponible son: 'Bartender' - 'cervecero' - 'cocinero' - 'mozo' - 'socio'"));
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
}
