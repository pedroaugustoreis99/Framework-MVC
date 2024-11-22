<?php

namespace middlewares;

class VerificarAutenticacaoMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: /login');
            exit;
        }
    }
}