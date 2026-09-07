<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function esUltimo(string $actual, string $proximo):bool {
    if( $actual != $proximo){
        return true;
    }
    return false;
    }

//Funcion que revisa si el usuario esta autenticado
function isAuth() : void  {
    if(!isset($_SESSION['login'])){ // pregunta si esta definida esta variable
        header('location: /');
    }
}

function isAdmin() : void {
    if (($_SESSION['rol'] ?? 'cliente') !== 'admin') {
        header('location: /');
        exit;//para que PHP no siga ejecutando el código de la página protegida después de ordenar la redirección.
    }
}