<?php

namespace MVC;

class Router
{
    public array $getRoutes = [];
    public array $postRoutes = [];

    public function get($url, $fn)
    {
        $this->getRoutes[$url] = $fn;
    }

    public function post($url, $fn)
    {
        $this->postRoutes[$url] = $fn;
    }

    public function comprobarRutas()
    {
        
//     echo "<pre>";
// var_dump($_SERVER['PATH_INFO'] ?? 'NO EXISTE');
// var_dump($_SERVER['REQUEST_URI'] ?? 'NO EXISTE');
// var_dump($_SERVER['REQUEST_METHOD'] ?? 'NO EXISTE');
// echo "</pre>";
// exit;
        
        // Proteger Rutas...
        //session_start();
        

        // Arreglo de rutas protegidas...
        // $rutas_protegidas = ['/admin', '/propiedades/crear', '/propiedades/actualizar', '/propiedades/eliminar', '/vendedores/crear', '/vendedores/actualizar', '/vendedores/eliminar'];

        // $auth = $_SESSION['login'] ?? null;

        //$currentUrl = $_SERVER['PATH_INFO'] ?? '/';
        //$currentUrl = strtoke($_SERVER['REQUEST_URI'],'?') ?? '/'; //opcion del tutorial extrae antes del ?
        $currentUrl =parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);//extrae solo el path
        $currentUrl = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $currentUrl = rtrim($currentUrl, '/') ?: '/'; // le quita el / al final a menos que sea un '' 
       // $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $fn = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $fn = $this->postRoutes[$currentUrl] ?? null;
        }


        if ( $fn ) {
            // Call user fn va a llamar una función cuando no sabemos cual sera
            call_user_func($fn, $this); // This es para pasar argumentos
        } else {
            echo "Página No Encontrada o Ruta no válida";
        }
    }


    public function render($view, $datos = [])
    {

        // Leer lo que le pasamos  a la vista
        foreach ($datos as $key => $value) {
            $$key = $value;  //Lo que hace $$key es crear una variable cuyo nombre está contenido dentro de otra variable.
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        // entonces incluimos la vista en el layout
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer
        include_once __DIR__ . '/views/layout.php';
    }
}
