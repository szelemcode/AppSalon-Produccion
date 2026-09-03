<?php

namespace Clases;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email,$nombre,$token)
    {
        $this->email=$email;
        $this->nombre=$nombre;
        $this->token=$token;
    }

    public function enviarConfirmacion(){
        //crear el objeto de email
        $mail=new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        // $mail->setFrom('cuentas@appsalon.com');//remplazamos por lo de debajo
       // $mail->addAddress('cuentas@appsalon.com','AppSalon.com');remplazamos por lo de debajo
        $mail->setFrom(
        $_ENV['EMAIL_FROM'],
        $_ENV['EMAIL_NAME']
        );

        $mail->addAddress(
            $this->email,
            $this->nombre
        );
        $mail->Subject='Confirma tu cuenta';

        //set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet='UTF-8';
        $contenido="<html>";
        $contenido .="<p><strong>Hola " .$this->nombre . "</strong> Has creado tu cuenta en App salon, solo debes confirmar
        presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí:
        <a href='" . $_ENV['APP_URL'] . "/confirmar-cuenta?token=" . $this->token . "'>
        Confirmar Cuenta
        </a></p>";
        $contenido .="<p>Si tu no solicitaste esta cuenta , puedes ignorar el mensaje </P>";
        $contenido .="</html>";

        $mail->Body = $contenido;

        //enviar mail
        $mail->send();
    }

    public function enviarInstrucciones(){

    //crear el objeto de email
        $mail=new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

       // $mail->setFrom('cuentas@appsalon.com');//remplazamos por lo de debajo
       // $mail->addAddress('cuentas@appsalon.com','AppSalon.com');remplazamos por lo de debajo
        $mail->setFrom(
        $_ENV['EMAIL_FROM'],
        $_ENV['EMAIL_NAME']
        );

        $mail->addAddress(
            $this->email,
            $this->nombre
        );

        $mail->Subject='Reestablece tu password';

        //set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet='UTF-8';
        $contenido="<html>";
        $contenido .="<p><strong>Hola " .$this->nombre . "</strong>Has solicitado
        reestablecer tu password, sigue el siguiente enlace para hacerlo</p>";
        $contenido .= "<p>Reestablecer:
        <a href='" . $_ENV['APP_URL'] . "/recuperar?token=" . $this->token . "'>
        Confirmar Cuenta
        </a></p>";
        $contenido .="<p>Si tu no solicitaste esta cuenta , puedes ignorar el mensaje </P>";
        $contenido .="</html>";

        $mail->Body = $contenido;

        //enviar mail
        $mail->send();
    }
}


?>