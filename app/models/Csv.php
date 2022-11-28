<?php

    class Csv
    {
        public static function crearCsvConDatosDeLaTabla()
        {
            $productos = Producto::obtenerTodos();
            $ruta = "./archivos/productos.csv";
            
            $file = fopen($ruta, "w+");
            foreach($productos as $unProducto)
            {
                if($file)
                {
                    fwrite($file, implode(",", (array)$unProducto)); 
                }                           
            }
            fclose($file);  

            return filesize($ruta)>0?true:false;
        }

        public static function cargarTablaConDatosDeUnCSv($file)
        {
            $todoOk=false;
            if(file_exists($file))
            {
                Producto::borrarTodosLosProductos();
                $archivo = fopen($file, "r");
                try
                {
                    while(!feof($archivo))
                    {
                        $datosDelProducto = fgets($archivo);                        
                        if(!empty($datosDelProducto))
                        {         
                            $unProducto=new Producto();

                            $arrayDePropiedades=explode(",", $datosDelProducto);
                            $id=$arrayDePropiedades[0];
                            $unProducto->nombre=$arrayDePropiedades[1];
                            $unProducto->precio=$arrayDePropiedades[2];
                            $unProducto->sectorEncargado=$arrayDePropiedades[3];
                            $todoOk=$unProducto->crearProducto();                       
                        }
                    }
                }
                catch(Exception $e)
                {
                    echo "No se pudo leer el archivo".$e->getMessage();
                    
                }
                finally
                {
                    fclose($archivo);
                    return $todoOk;
                }
                
            }
        }
    }
?>

