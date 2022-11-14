<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
class Empleado
{
    public $id;
    public $nombre;
    public $nickNameUser;
    public $clave;
    public $fechaAlta;
    public $tipoDePersonal;
    public $fechaBaja;
    
    public function crearEmpleado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
        "INSERT INTO empleados (nombre, nickNameUser, clave, fechaAlta, tipoDePersonal) 
         VALUES (:nombre, :nickNameUser, :clave, :fechaAlta, :tipoDePersonal)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':nickNameUser', $this->nickNameUser, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':tipoDePersonal', $this->tipoDePersonal, PDO::PARAM_STR);
        $consulta->bindValue(':fechaAlta', date('Y/m/d H:i:s') , PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleados");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function obtenerEmpleado($nickDelUsuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, nickNameUser, clave, fechaAlta, tipoDePersonal, fechaBaja FROM empleados WHERE nickNameUser = :nick");
        $consulta->bindValue(':nick', $nickDelUsuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }
}