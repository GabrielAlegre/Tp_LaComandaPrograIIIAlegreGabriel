<?php
require_once './models/Producto.php';
require_once './models/Pedido.php';
require_once './models/Csv.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      if(!empty($parametros['nombre']) && !empty($parametros['precio']) && !empty($parametros['sectorEncargado']))
      {
        $producto = new Producto();
        $producto->nombre = $parametros['nombre'];
        $producto->precio = $parametros['precio'];
        $producto->sectorEncargado = $parametros['sectorEncargado'];
        $producto->crearProducto();
        $payload = json_encode(array("mensaje" => "Producto creado con exito, se agrego al menu!"));
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
        $payload = json_encode(array("listaDeProductos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function crearCsvConDatosDeUnaTabla($request, $response, $args)
    {

      $payload =Csv::crearCsvConDatosDeLaTabla()?json_encode(array("mensaje" => "Se cargo el csv con datos exitosamente!"))
      :json_encode(array("mensaje" => "Error, verifique la informacion ingresada"));
      $response->getBody()->write( $payload);
   

      return $response->withHeader('Content-Type', 'application/json');;
    }

    public function cargarTablaConDatosDelCSv($request, $response, $args)
    {
      try
      {
        $file=$_FILES["archivoCSV"]["tmp_name"];
        $seCargo=Csv::cargarTablaConDatosDeUnCSv($file);
        if($seCargo)
        {
          readfile($file);
          $res=$response->withHeader('Content-Type', 'text/csv');
        }
        else
        {
          $res=$response->withHeader('Content-Type', 'application/json');
          $response->getBody()->write(json_encode(array("mensaje" => "Error, verifique la informacion ingresada")));
        }
      }
      catch(Exception  $e)
      {
        printf("Excepción capturada: {$e->getMessage()}");
      }
      finally
      {
        return $res;
      }    
    }

}
