<?php

namespace controllers;

use models\LoginModel;

class HomeController
{
    public function home()
    {
        $dados['titulo'] = 'Home';
        
        $login_model = new LoginModel();
        $dados['usuario'] = $login_model->retorna_usuario($_SESSION['id_usuario']);

        view('home/home', $dados);
    }
}