<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
class ordenProducto
{
    public $id;
    public $idDelProductoElegido;
    public $nroDePedidoAlQueCorrespondeLaOrden;
    public $estado;
    public $tiempoEstimadoDePreparacion;

    public function crearUnaOrdenDeProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO ordenes_productos (idDelProductoElegido, nroDePedidoAlQueCorrespondeLaOrden, estado) 
            VALUES (:idPlato, :nroPedidoRelacionado, :estado)");

        $consulta->bindValue(':idPlato', $this->idDelProductoElegido, PDO::PARAM_STR);
        $consulta->bindValue(':nroPedidoRelacionado', $this->nroDePedidoAlQueCorrespondeLaOrden, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ordenes_productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ordenProducto');
    }

    
    public static function obtenerOrdenesPorSuEstadoAndSector($numeroDePedido, $tipoDeEmpleado, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "SELECT * FROM ordenes_productos INNER JOIN productos on ordenes_productos.idDelProductoElegido=productos.id 
        WHERE ordenes_productos.nroDePedidoAlQueCorrespondeLaOrden = :numPedido AND productos.sectorEncargado = :sector AND ordenes_productos.estado= :estado ");
        $consulta->bindValue(':numPedido', $numeroDePedido, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $tipoDeEmpleado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ordenProducto');
    }

    public static function actualizarEstadoOrdenAndTiempoPreparacionPorSector($nroDelPedidoRelacionado, $nuevoEstado, $nuevoTiempo, $tipoDeEmpleado, $idEmpleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "UPDATE ordenes_productos  INNER JOIN productos on ordenes_productos.idDelProductoElegido=productos.id 
        set ordenes_productos.estado = :nuevoEstado, ordenes_productos.tiempoEstimadoDePreparacion = :tiempo, ordenes_productos.idEmpleadoQuePrepararaLaOrden = :id
        WHERE nroDePedidoAlQueCorrespondeLaOrden = :numPedido AND productos.sectorEncargado = :sector AND ordenes_productos.estado = 'pendiente'
        LIMIT 1");
        $consulta->bindValue(':numPedido', $nroDelPedidoRelacionado, PDO::PARAM_STR);
        $consulta->bindValue(':nuevoEstado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $nuevoTiempo);
        $consulta->bindValue('sector', $tipoDeEmpleado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $idEmpleado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function actualizarEstadoEnListoParaServir($nroDelPedidoRelacionado, $nuevoEstado, $tipoDeEmpleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "UPDATE ordenes_productos  INNER JOIN productos on ordenes_productos.idDelProductoElegido=productos.id 
        set ordenes_productos.estado = :nuevoEstado WHERE nroDePedidoAlQueCorrespondeLaOrden = :numPedido AND productos.sectorEncargado = :sector AND ordenes_productos.estado = 'en preparacion'
        LIMIT 1");
        $consulta->bindValue(':numPedido', $nroDelPedidoRelacionado, PDO::PARAM_STR);
        $consulta->bindValue(':nuevoEstado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $tipoDeEmpleado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function obtenerOrden($idDeLaOrden)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ordenes_productos WHERE id = :id");
        $consulta->bindValue(':id', $idDeLaOrden, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('ordenProducto');
    }

    public static function traerTodasLasOrdenesDeUnPedido($numPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ordenes_productos WHERE nroDePedidoAlQueCorrespondeLaOrden = :numPedido");
        $consulta->bindValue(':numPedido', $numPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ordenProducto');
    }

    public static function actualizarEstadoOrdenes($nroDelPedidoRelacionado, $nuevoEstado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "UPDATE ordenes_productos SET ordenes_productos.estado = :nuevoEstado WHERE nroDePedidoAlQueCorrespondeLaOrden = :numPedido");
        $consulta->bindValue(':numPedido', $nroDelPedidoRelacionado, PDO::PARAM_STR);
        $consulta->bindValue(':nuevoEstado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->rowCount();
    }

    /*
    public static function asignarEmpleadoQuePrepararaLaOrden($nroDelPedidoRelacionado, $idEmpleado, $sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "UPDATE ordenes_productos  INNER JOIN productos on ordenes_productos.idDelProductoElegido=productos.id 
        SET ordenes_productos.idEmpleadoQuePrepararaLaOrden = :id WHERE nroDePedidoAlQueCorrespondeLaOrden = :numPedido AND productos.sectorEncargado = :sector AND ordenes_productos.estado = 'pendiente'
        LIMIT 1");
        $consulta->bindValue(':numPedido', $nroDelPedidoRelacionado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $idEmpleado, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->rowCount();
    }*/

    
}