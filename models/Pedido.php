<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
class Pedido
{
    public $id;
    public $nombreDelCliente;
    public $nroDePedido;
    public $estado;
    public $codigoDeMesaAsociada;
    public $tiempoDeFinalizacionEstimado;
    public $precioTotal;
    public $pathFoto;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "INSERT INTO pedidos (nombreDelCliente, nroDePedido, estado, codigoDeMesaAsociada, pathFoto) 
         VALUES (:nombreCliente, :numPedido, :estado, :codigoMesa, :pathImg)");

        $consulta->bindValue(':nombreCliente', $this->nombreDelCliente, PDO::PARAM_STR);
        $consulta->bindValue(':numPedido', $this->nroDePedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $this->codigoDeMesaAsociada, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "Esperando que el cliente ordene" , PDO::PARAM_STR);
        if(empty($this->pathFoto))
        {
            $consulta->bindValue(':pathImg', NULL , PDO::PARAM_BOOL);
        }
        else
        {
            $consulta->bindValue(':pathImg', $this->pathFoto , PDO::PARAM_STR);
        }
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($nroDePedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE nroDePedido = :numPedido");
        $consulta->bindValue(':numPedido', $nroDePedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function actualizarEstadoPedido($nroDelPedidoQueSeModificara, $nuevoEstado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos set estado = :nuevoEstado WHERE nroDePedido = :numPedido");
        $consulta->bindValue(':numPedido', $nroDelPedidoQueSeModificara, PDO::PARAM_STR);
        $consulta->bindValue(':nuevoEstado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public static function asignarleTiempoDePreparacionEstimado($nroDelPedidoQueSeModificara, $tiempo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos set tiempoDeFinalizacionEstimado = :tiempo WHERE nroDePedido = :numPedido");
        $consulta->bindValue(':numPedido', $nroDelPedidoQueSeModificara, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public static function asignarlePrecioTotalAlPedido($nroDelPedidoQueSeModificara, $precio)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos set precioTotal = :precio WHERE nroDePedido = :numPedido");
        $consulta->bindValue(':numPedido', $nroDelPedidoQueSeModificara, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $precio, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public static function obtenerTodosLoPedidosQueEstanListosParaServir()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE estado = 'listo para servir'");
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }



    public static function obtenerPrecioTotalDelPedido($numeroPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "SELECT SUM(productos.precio) FROM ordenes_productos INNER JOIN productos on ordenes_productos.idDelProductoElegido=productos.id
         WHERE ordenes_productos.nroDePedidoAlQueCorrespondeLaOrden = :numPedido");
        $consulta->bindValue(':numPedido', $numeroPedido, PDO::PARAM_STR);

        $consulta->execute();
        
        return $consulta->fetchColumn();
    }

    
}