<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa
{
    public function CargarUno($request, $response, $args)
    {
        $codigo=MesaController::crearCodigoMesa();
        $mesa = new Mesa();
        $mesa->codigoDeMesa = $codigo;
        $mesa->estado = "Libre";
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
}
