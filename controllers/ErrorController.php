<?php

require_once 'BaseController.php';

class ErrorController extends BaseController {
    public function notFound() {
        header("HTTP/1.0 404 Not Found");
        $this->render('views/error/404.php');
    }

    public function accessDenied() {
        header("HTTP/1.0 403 Forbidden");
        $this->render('views/error/403.php'); // Asumiendo que crearás una vista 403.php
    }

    public function generalError($message = "Ha ocurrido un error inesperado.") {
        // Aquí podrías loggear el error para depuración
        $this->render('views/error/general.php', ['message' => $message]); // Asumiendo una vista general.php
    }
}
