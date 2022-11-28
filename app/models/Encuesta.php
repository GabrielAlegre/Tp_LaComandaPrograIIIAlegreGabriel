<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
class Encuesta
{
    public $id;
    public $puntajeMesa;
    public $puntajeResto;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $promedio;
    public $descripcion;
    public $numeroDelPedido;
    public $codigoDeLaMesaUtilizada;
    
    public static function crearEncuensta($puntajeMesa, $puntajeResto, $puntajeMozo, $puntajeCocinero, $promedio, $descripcion, $numeroDelPedido, $codigoDeLaMesaUtilizada)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "INSERT INTO encuestas (puntajeMesa, puntajeResto, puntajeMozo, puntajeCocinero, promedio, descripcion, numeroDelPedido, codigoDeLaMesaUtilizada) 
         VALUES (:notaMesa, :notaResto, :notaMozo, :notaCocinero, :promedio, :descripcion, :numPedido, :codigoMesa)");

        $consulta->bindValue(':notaMesa', $puntajeMesa);
        $consulta->bindValue(':notaResto', $puntajeResto);
        $consulta->bindValue(':notaMozo', $puntajeMozo);
        $consulta->bindValue(':notaCocinero', $puntajeCocinero);
        $consulta->bindValue(':promedio', $promedio);
        $consulta->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':numPedido', $numeroDelPedido, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $codigoDeLaMesaUtilizada, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerMejoresComentarios()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT promedio, descripcion FROM encuestas WHERE promedio BETWEEN '8' AND '10'");

        $consulta->execute();
       

        return $consulta->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
    }
    
}