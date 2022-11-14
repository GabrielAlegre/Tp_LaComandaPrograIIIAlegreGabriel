<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
class Producto
{
    public $id;
    public $nombre;
    public $precio;
    public $cantidad;
    public $sectorEncargado;
    public $tiempoDePreparacion;
    public $numeroDePedido;
    public $estado;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "INSERT INTO productos (nombre, precio, cantidad, sectorEncargado, tiempoDePreparacion, numeroDePedido, estado) 
         VALUES (:nombreDelProducto, :precio, :cantidad, :sector, :tiempo, :numeroPedido, :estado)");

        $consulta->bindValue(':nombreDelProducto', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':sector', $this->sectorEncargado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $this->tiempoDePreparacion, PDO::PARAM_INT);
        $consulta->bindValue(':numeroPedido', $this->numeroDePedido, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerProducto($idDelProducto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $idDelProducto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }
}