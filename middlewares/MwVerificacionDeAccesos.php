<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MwVerificacionDeAccesos{

    public function esSocio($request, $handler) {
        $response = new Response();
        $header = $request->getHeaderLine('authorization');

        if (!empty($header))
        {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                AutentificadorJWT::verificarToken($token);

                $data = AutentificadorJWT::ObtenerData($token);
                
                if ($data->tipo == "socio") {
                    $response = $handler->handle($request);
                    $payload = json_encode(array("Ok" => "Token {$data->tipo} valido"));
                } else {
                    $payload = json_encode(array("Error" => "Solo tienen acceso a realizar esta accion los socios"));
                    $response = $response->withStatus(401);
                }
                
            } catch (Exception $e) {
                $payload = json_encode(array('Error:' => $e->getMessage()));
                $response=$response->withStatus(401);
            }
        } 
        else 
        {
            $payload = json_encode(array("Error: " => "Para realizar esta accion se necesita enviar un token, por favor, envielo"));
            $response = $response->withStatus(401);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function esMozo($request, $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('authorization');

        if (!empty($header))
        {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                AutentificadorJWT::verificarToken($token);
                $data = AutentificadorJWT::ObtenerData($token);

                if ($data->tipo == "socio" || $data->tipo == "mozo") {
                    $response = $handler->handle($request);
                    $payload = json_encode(array("Ok" => "Token {$data->tipo} valido"));
                } else {
                    $payload = json_encode(array("Error" => "Solo tienen acceso a realizar esta accion los mozos"));
                    $response = $response->withStatus(401);
                }
                
            } catch (Exception $e) {
                $payload = json_encode(array('Errrrrrror:' => $e->getMessage()));
                $response=$response->withStatus(401);
            }
        } 
        else 
        {
            $payload = json_encode(array("Error: " => "Para realizar esta accion se necesita enviar un token, por favor, envielo"));
            $response = $response->withStatus(401);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function esCocinero($request, $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('authorization');

        if (!empty($header))
        {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                AutentificadorJWT::verificarToken($token);
                $data = AutentificadorJWT::ObtenerData($token);

                if ($data->tipo == "socio" || $data->tipo == "cocinero") {
                    $response = $handler->handle($request);
                    $payload = json_encode(array("Ok" => "Token {$data->tipo} valido"));
                } else {
                    $payload = json_encode(array("Error" => "Solo tienen acceso a realizar esta accion los cocineros"));
                    $response = $response->withStatus(401);
                }
                
            } catch (Exception $e) {
                $payload = json_encode(array('error:' => $e->getMessage()));
                $response=$response->withStatus(401);
            }
        } 
        else 
        {
            $payload = json_encode(array("error: " => "Para realizar esta accion se necesita enviar un token, por favor, envielo"));
            $response = $response->withStatus(401);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function esCervecero($request, $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('authorization');

        if (!empty($header))
        {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                AutentificadorJWT::verificarToken($token);
                $data = AutentificadorJWT::ObtenerData($token);

                if ($data->tipo == "socio" || $data->tipo == "cervecero") {
                    $response = $handler->handle($request);
                    $payload = json_encode(array("Ok" => "Token {$data->tipo} valido"));
                } else {
                    $payload = json_encode(array("Error" => "Solo tienen acceso a realizar esta accion los cervecero"));
                    $response = $response->withStatus(401);
                }
                
            } catch (Exception $e) {
                $payload = json_encode(array('Errrrrrror:' => $e->getMessage()));
                $response=$response->withStatus(401);
            }
        } 
        else 
        {
            $payload = json_encode(array("Error: " => "Para realizar esta accion se necesita enviar un token, por favor, envielo"));
            $response = $response->withStatus(401);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function esBartender($request, $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('authorization');

        if (!empty($header))
        {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                AutentificadorJWT::verificarToken($token);
                $data = AutentificadorJWT::ObtenerData($token);

                if ($data->tipo == "socio" || $data->tipo == "bartender") {
                    $response = $handler->handle($request);
                    $payload = json_encode(array("Ok" => "Token {$data->tipo} valido"));
                } else {
                    $payload = json_encode(array("Error" => "Solo tienen acceso a realizar esta accion los bartender"));
                    $response = $response->withStatus(401);
                }
                
            } catch (Exception $e) {
                $payload = json_encode(array('Error:' => $e->getMessage()));
                $response=$response->withStatus(401);
            }
        } 
        else 
        {
            $payload = json_encode(array("Error: " => "Para realizar esta accion se necesita enviar un token, por favor, envielo"));
            $response = $response->withStatus(401);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function esEmpleado($request, $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('authorization');

        if (!empty($header))
        {
            $token = trim(explode("Bearer", $header)[1]);
            try {
                AutentificadorJWT::verificarToken($token);
                $data = AutentificadorJWT::ObtenerData($token);

                if ($data->tipo == "socio" || $data->tipo == "bartender" || $data->tipo == "cocinero" || $data->tipo == "cervecero") {
                    $response = $handler->handle($request);
                    $payload = json_encode(array("Ok" => "Token {$data->tipo} valido"));
                } else {
                    $payload = json_encode(array("Error" => "Solo tienen acceso a realizar el cambio del estado de un producto los bartender - cocinero - cervecero"));
                    $response = $response->withStatus(401);
                }
                
            } catch (Exception $e) {
                $payload = json_encode(array('Error:' => $e->getMessage()));
                $response=$response->withStatus(401);
            }
        } 
        else 
        {
            $payload = json_encode(array("Error: " => "Para realizar esta accion se necesita enviar un token, por favor, envielo"));
            $response = $response->withStatus(401);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}