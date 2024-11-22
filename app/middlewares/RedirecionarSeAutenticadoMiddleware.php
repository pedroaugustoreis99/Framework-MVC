<?php

namespace middlewares;

class RedirecionarSeAutenticadoMiddleware
{
    public function handle()
    {
        if (isset($_SESSION['id_usuario'])) {
            header('Location: /home');
            exit;
        }
    }
}