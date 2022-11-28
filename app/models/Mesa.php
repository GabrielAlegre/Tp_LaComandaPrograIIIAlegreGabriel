<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
class Mesa
{
    public $id;
    public $codigoDeMesa;
    public $estado;    

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "INSERT INTO mesas (codigoDeMesa, estado) 
         VALUES (:codigo, :estado)");
        $consulta->bindValue(':codigo', $this->codigoDeMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($codigoDeMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE codigoDeMesa = :codigo");
        $consulta->bindValue(':codigo', $codigoDeMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function actualizarEstadoMesa($codigoDeMesaQueSeModificara, $nuevoEstado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas set estado = :nuevoEstado WHERE codigoDeMesa = :codigo");
        $consulta->bindValue(':codigo', $codigoDeMesaQueSeModificara, PDO::PARAM_STR);
        $consulta->bindValue(':nuevoEstado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function cobrarMesaYCambiarEstado($codigoDeMesaQueSeModificara, $nuevoEstado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas set estado = :nuevoEstado WHERE codigoDeMesa = :codigo AND estado = 'con cliente comiendo'");
        $consulta->bindValue(':codigo', $codigoDeMesaQueSeModificara, PDO::PARAM_STR);
        $consulta->bindValue(':nuevoEstado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function cerrarMesa($codigoDeMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas set estado = 'cerrada' WHERE codigoDeMesa = :codigo AND estado = 'con cliente pagando'");
        $consulta->bindValue(':codigo', $codigoDeMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function obtenerTiempoEspera($nroPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(tiempoEstimadoDePreparacion) 
        FROM ordenes_productos WHERE nroDePedidoAlQueCorrespondeLaOrden = :numPedido AND estado = 'en preparacion'");
        $consulta->bindValue(':numPedido', $nroPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchColumn();
    }

}