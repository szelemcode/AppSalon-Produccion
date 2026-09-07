<?php
namespace Controllers;
require_once '../includes/app.php';

use Clases\Email;
use Model\Usuario;
use MVC\Router;


class LoginController {
    public static function login(Router $router){
        $alertas=[];
        $auth=new Usuario();

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $auth=new Usuario($_POST);

       $alertas=$auth->validarLogin();

       if(empty($alertas)){
        //COMPROBAR SI EXISTE USUARIO   
        $usuario=Usuario::where('email',$auth->email);
        if($usuario) {
            //Verificar el passoword
           if( $usuario->comprobarPasswordAndVerificado($auth->password)){
            // si todo esta bien autenticar usuario
            //session_start();
            $_SESSION['id']=$usuario->id;
            $_SESSION['nombre']=$usuario->nombre . " " .$usuario->apellido;
            $_SESSION['email']=$usuario->email;
            $_SESSION['login']=true;

            //Redireccionar
            if($usuario->rol === "admin"){
                $_SESSION['rol'] = $usuario->rol ?? 'cliente';
                
                header('Location:/admin');
            }else{
                header('Location:/cita');
            }
           }
        }else{
            //set alertas
            Usuario::setAlerta('error','Usuario no encontrado');
        }
        //debuguear($usuario);
       }
       
       $alertas=Usuario::getAlertas();

    }
        $router->render('auth/login',[
            'alertas'=>$alertas,
            'auth'=>$auth
        ]);
    }

    public static function logout(){
        //session_start(); // esta linea no es necesaria ya que la llamo desde includes/app.php
        $_SESSION = [];//limpio la sesion
        header('location: /');
    }

    public static function olvide(Router $router){
        $alertas=[];
       // $auth=new Usuario;
        
        if($_SERVER['REQUEST_METHOD']==='POST'){
          
            $auth=new Usuario($_POST);
           //debuguear($auth);
            $alertas=$auth->validarEmail();
            if(empty($alertas)){
                $usuario=Usuario::where('email',$auth->email);

                if($usuario && $usuario->confirmado ==="1"){
                    //Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //ENVIAR EMAIL
                    $email=new Email($usuario->email,$usuario->nombre,$usuario->token);
                    $email->enviarInstrucciones();

                    //Alerta de exito
                    Usuario::setAlerta('exito','Revisa tu email');

                }else{
                    Usuario::setAlerta('error','El usuario no existe o no esta confirmado');
                    
                }
                
                }            
        }

        $alertas= Usuario::getAlertas();

        $router->render('auth/olvide-password',[
            'alertas'=>$alertas
        ]);
    }

    public static function recuperar(Router $router){
        $alertas=[];
        $error=false;

        $token=s($_GET['token']);//s funcion para sanitizar
        //Buscar Usuario por su token
        $usuario = Usuario::where('token', $token);//objeto usuario con la info de la base de datos
        if(empty($usuario)){
            Usuario::setAlerta('error','Token no valido');
            $error=true;
        }

        if($_SERVER['REQUEST_METHOD']==='POST'){
            //Leer el nuevo password y guardarlo
            $password=new Usuario($_POST);
            $alertas=$password->ValidarPassword();

            if(empty($alertas)){
                $usuario->password=null;//borro el password anterior del objeto usuario(no de la base de datos)
                $usuario->password= $password->password;//asigno el password del objeto password al objeto usuario
                $usuario->hashPassword();//hasheamos el password
                $usuario->token=null;;// cambiamos el token a null para que pase la validacion al ingresar
                $resultado=$usuario->guardar();
                if($resultado){//la funcion guardar devuelve un resultado
                    header('Location:/');
                }
                //debuguear($usuario);

            }
        }
        
        $alertas=Usuario::getAlertas();
        $router->render('auth/recuperar-password',[
            'alertas'=>$alertas,
            'error'=>$error

        ]);
    }

    public static function crear(Router $router){
        $usuario= new Usuario;

        //Alertas vacias
        $alertas =[];
        if($_SERVER['REQUEST_METHOD']==='POST'){
            
            $usuario->sincronizar($_POST);
            $alertas=$usuario->validarNuevaCuenta();
            
            //Revisar que las alertas esten vacias
            if(empty($alertas)){
               $resultado= $usuario->existeUsuario();
               if($resultado->num_rows){
                    $alertas=Usuario::getAlertas();
               }else{

                //Hashear el password
                $usuario->hashPassword();

                //Generar Token unico
                $usuario->crearToken();

                //Enviar Email
                $email=new Email($usuario->email,$usuario->nombre,$usuario->token);
                $email->enviarConfirmacion();

                //crear usuario
                $resultado=$usuario->guardar();
                if($resultado){
                    header('location: /mensaje');
                }
                //debuguear($usuario);
               }
            }
        }
        $router->render('auth/crear-cuenta',[
            'usuario'=>$usuario,
            'alertas'=>$alertas
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
}

    public static function confirmar(Router $router){
        $alertas=[];

        $token=s($_GET['token']);
        $usuario=Usuario::where('token',$token);

        if(empty($usuario)){
            //Mostrar mensaje de erro
            Usuario::setAlerta('error','Token no valido');
        }else{
            //Modificar a usuario confirmado
            $usuario->confirmado="1";
            $usuario->token=null;
            $usuario->guardar();
            Usuario::setAlerta('exito','Cuenta comprobada Correctamente');
        }

        //obtener alertas
        $alertas=Usuario::getAlertas();

        //Renderizar la vista
        $router->render('auth/confirmar-cuenta',[
            'alertas'=>$alertas
        ]);
    }

    public static function cita(Router $router){
        //echo "desde cita";
        $router->render('servicios/cita');
    }
}