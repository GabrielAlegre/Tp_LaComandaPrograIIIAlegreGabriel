<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
class Pedido
{
    public $id;
    public $nombreDelCliente;
    public $nroDePedido;
    public $tiempoDeFinalizacionEstimado;
    public $fechaDeCuandoSeTomoElPedido;
    public $estado;
    public $codigoDeMesaAsociada;
    public $pathFoto;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "INSERT INTO pedidos (nombreDelCliente, nroDePedido, fechaDeCuandoSeTomoElPedido, estado, codigoDeMesaAsociada, pathFoto) 
         VALUES (:nombreCliente, :numPedido, :fechaDeCuandoSeTomoElPedido, :estado, :codigoMesa, :pathImg)");

        $consulta->bindValue(':nombreCliente', $this->nombreDelCliente, PDO::PARAM_STR);
        $consulta->bindValue(':numPedido', $this->nroDePedido, PDO::PARAM_STR);
        $consulta->bindValue(':fechaDeCuandoSeTomoElPedido', $this->fechaDeCuandoSeTomoElPedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $this->codigoDeMesaAsociada, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "Esperando que productos va a pedir el cliente" , PDO::PARAM_STR);
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
}