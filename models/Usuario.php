<?php
namespace Model;

include_once __DIR__ . '/../includes/app.php';

class Usuario extends ActiveRecord{
    //base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','nombre','apellido','email','password','telefono','rol','confirmado','token'];
    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $rol;
    public $confirmado;
    public $token;

    public function __construct($args=[]) {
        $this->id=$args['id'] ?? null;
        $this->nombre=$args['nombre'] ?? null;
        $this->apellido=$args['apellido'] ?? null;
        $this->email=$args['email'] ?? null;
        $this->password=$args['password'] ?? null;
        $this->telefono=$args['telefono'] ?? null;
        $this->rol=$args['rol'] ?? 'cliente';
        $this->confirmado=$args['confirmado'] ?? 0;
        $this->token=$args['token'] ?? '';
    }

    //Mensajes de validacion para la creacion de cuenta
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][]="El nombre es obligatorio";
        }
        if(!$this->apellido){
            self::$alertas['error'][]="El apellido es obligatorio";
        }
        if(!$this->email){
            self::$alertas['error'][]="El email es obligatorio";
        }
        if(!$this->password){
            self::$alertas['error'][]="el password es obligatorio";
        }
        if(strlen($this->password) >0 && strlen($this->password)<6 ){
            self::$alertas['error'][]="El password tiene que tener 6 o mas caracteres";
        }
        if(!$this->telefono){
            self::$alertas['error'][]="El telefon es obligatorio";
        }
        return self::$alertas;
    }


    //VAlidar login
     public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][]="El email es obligatorio";
        }
        if(!$this->password){
            self::$alertas['error'][]="El password es obligatorio";
        }
        return self::$alertas;
    }

     //VALIDAR EMAIL
     public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][]='El mail es obligatorio';
        }
        return self::$alertas;
     }

     //VALIDAR PASSWORD
     public function validarPassword(){
        if(!$this->password){
            // self::$alertas['error'][]='El Password es obligatorio';
            self::setAlerta('error','el password es obligatorio');//puede ser con la funcion o llamando a la variable como en el otro ejemplo
        }
        if(strlen($this->password)<6){
            self::$alertas['error'][]='El Password debe tener al menos 6 caracterers';
        }

        return self::$alertas;
     }



    //Revusa si el usuario ya existe
    public function existeUsuario(){
        $query=" SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        $resultado=self::$db->query($query);
       //debuguear($resultado);

        if($resultado->num_rows){
            self::$alertas['error'][]="El usuario ya esta registrado";
        }
        return $resultado;
    }

    public function hashPassword(){
        $this->password=password_hash($this->password,PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token=uniqid();
    }

   public function comprobarPasswordAndVerificado($password){
    $resultado=password_verify($password, $this->password);
    if(!$resultado || !$this->confirmado){
        self::$alertas['error'][]="Password Incorrecto o tu cuenta no ha sido confirmada";
    }else{
        return true;
    }
   }
}
?>