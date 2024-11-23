<?php

namespace controllers;

use helpers\Log;
use models\LoginModel;

class LoginController
{
    /*
     * Propriedade responsável por armazenar erros de validação de formulários
     */
    private $erros = array();

    /*
     * Constantes que indicam o status da operação: criando ou atualizando um usuário.
     * Essas constantes são utilizadas no parâmetro $status_operacao no método auxiliar validar_campos()
     */
    const STATUS_CRIANDO = 1,
        STATUS_ATUALIZANDO = 2;

    /*
     * Actions utilizadas para processar requisições HTTP
     */
    public function formulario_login()
    {
        $dados['titulo'] = 'Formulário de login';

        if (isset($_SESSION['erros_de_validacao'])) {
            $dados['erros_de_validacao'] = $_SESSION['erros_de_validacao'];
            unset($_SESSION['erros_de_validacao']);
        }

        view('login/formulario_login', $dados);
    }

    public function login()
    {
        /*
         * Verifica se os campos necessários para o cadastro estão definidos em $_POST.
         */
        extract($_POST);
        if (!isset($usuario) OR !isset($senha)) {
            Log::critical('O usuário realizou uma requisição POST para a Action ' . __METHOD__ . ' com dados incompletos. Está faltando $_POST["usuario"] ou $_POST["senha"]');
            view('sistema/erro-interno');
            exit;
        }

        $login_model = new LoginModel();
        if ($login_model->verificar_login($usuario, $senha)) {
            $login_model->definir_ultimo_login($usuario);
            $_SESSION['id_usuario'] = $login_model->retorna_id($usuario);
            header('Location: /home');
        } else {
            $_SESSION['erros_de_validacao'] = 'Credenciais incorretas, tente novamente!';
            header('Location: /login');
            exit;
        }
    }

    public function logout()
    {
        unset($_SESSION['id_usuario']);
        header('Location: /');
    }
    
    public function formulario_cadastro()
    {
        $dados['titulo'] = 'Formulário de cadastro de usuário';

        if (isset($_SESSION['erros_de_validacao'])) {
            $dados['erros_de_validacao'] = $_SESSION['erros_de_validacao'];
            unset($_SESSION['erros_de_validacao']);
        }

        view('login/formulario_cadastro', $dados);
    }

    public function cadastrar()
    {
        /*
         * Verifica se os campos necessários para o cadastro estão definidos em $_POST.
         */
        extract($_POST);
        if (!isset($usuario) OR !isset($senha) OR !isset($confirmar_senha)) {
            Log::critical('O usuário realizou uma requisição via POST para a Action ' . __METHOD__ . ' com dados incompletos. Está faltando $_POST["usuario"] ou $_POST["senha"] ou $_POST["confirmar_senha"]');
            view('sistema/erro-interno');
            exit;
        } 

        /*
         * Validação dos campos e caso seja necessário envia mensagens de erro para o formulário de cadastro.
         */
        $this->validar_campos($usuario, $senha, $confirmar_senha, LoginController::STATUS_CRIANDO);
        if (!empty($this->erros)) {
            $_SESSION['erros_de_validacao'] = $this->erros;
            header('Location: /cadastrar-usuario');
            exit;
        }

        /*
         * Registra um novo usuário no banco de dados.
         */
        $login_model = new LoginModel();
        $resultado = $login_model->cadastrar($usuario, $senha);
        if ($resultado->status == 'success') {
            // Depois vou criar uma view informando que o usuário foi cadastrado com sucesso!
            echo 'Usuário cadastrado com sucesso!';
        } else if ($resultado->status == 'error') {
            Log::alert("Houve um erro na action " . __METHOD__ . " ao tentar cadastrar um usuário. O erro produziu a seguinte mensagem: " . $resultado->msg);
            view('sistema/erro-interno');
        }
    }

    public function editar()
    {
        $dados['titulo'] = 'editar usuário';
        $login_model = new LoginModel();
        $dados['usuario'] = $login_model->retorna_usuario($_SESSION['id_usuario']);

        if (isset($_SESSION['erros_de_validacao'])) {
            $dados['erros_de_validacao'] = $_SESSION['erros_de_validacao'];
            unset($_SESSION['erros_de_validacao']);
        }
        
        view('login/formulario_editar', $dados);
    }
    public function atualizar()
    {
        /*
         * Verifica se os campos necessários para a atualização estão definidos em $_POST.
         */
        extract($_POST);
        if (!isset($senha_atual) OR !isset($usuario) OR !isset($senha) OR !isset($confirmar_senha)) {
            Log::critical('O usuário realizou uma requisição via POST para a Action ' . __METHOD__ . ' com dados incompletos. Está faltando $_POST["senha_atual"] ou $_POST["usuario"] ou $_POST["nova_senha"] ou $_POST["confirmar_nova_senha"]');
            view('sistema/erro-interno');
            exit;
        }

        $login_model = new LoginModel();
        /*
         * Verifica se a senha atual está correta
         */
        $usuario_atual = $login_model->retorna_usuario($_SESSION['id_usuario']);
        if (!$login_model->verificar_login($usuario_atual, $senha_atual)) {
            $this->erros['senha_atual'] = 'A senha digitada está incorreta!';
        }
        $this->validar_campos($usuario, $senha, $confirmar_senha, LoginController::STATUS_ATUALIZANDO, $_SESSION['id_usuario']);

        if (!empty($this->erros)) {
            $_SESSION['erros_de_validacao'] = $this->erros;
            header('Location: /editar-perfil');
            exit;
        }

        $resultado = $login_model->atualizar($_SESSION['id_usuario'], $usuario, $senha);
        if ($resultado->status == 'success') {
            header('Location: /home');
        } else if ($resultado->status == 'error') {
            Log::alert("Houve um erro na action " . __METHOD__ . " ao tentar atualizar um usuário. O erro produziu a seguinte mensagem: " . $resultado->msg);
            view('sistema/erro-interno');
        }
    }

    /*
     * Métodos auxiliares que serão utilizados nas Actions.
     */
    private function validar_campos($usuario, $senha, $confirmar_senha, $status_operacao, $id = null)
    {
        $login_model = new LoginModel();

        /*
         * Verificando se os dados vão ser utilizados para criar ou atualizar um usuário.
         */
        if ($status_operacao == LoginController::STATUS_CRIANDO) {
            $usuario_existe = $login_model->usuario_existe_criando($usuario);
        } else if ($status_operacao == LoginController::STATUS_ATUALIZANDO) {
            $usuario_existe = $login_model->usuario_existe_atualizando($id, $usuario);
        } else {
            // Depois tenho que implementar uma lógica para esse caso!
        }
        
        /*
         * Validações do campo usuário.
         */
        if (empty($usuario)) {
            $this->erros['usuario'] = "Digite um usuário.";
        } else if (strlen($usuario) < 5 OR strlen($usuario) > 60) {
            $this->erros['usuario'] = "O campo usuário deve ter entre 5 e 60 caracteres.";
        } else if ($usuario_existe) {
            $this->erros['usuario'] = 'O usuário digitado já existe, digite outro usuário.';
        }

        /*
         * Validações do campo senha.
         */
        if (empty($senha)) {
            $this->erros['senha'] = 'Digite uma senha.';
        } else if (strlen($senha) < 5 OR strlen($senha) > 60) {
            $this->erros['senha'] = 'O campo senha deve ter entre 5 e 60 caracteres.';
        } else if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/", $senha)) {
            $this->erros['senha'] = 'A senha deve conter pelo menos uma letra maiúscula, pelo menos uma letra minúscula e pelo menos um número.';
        }

        /*
         * Validação do campo confirmar_senha.
         */
        if ($confirmar_senha != $senha) {
            $this->erros['confirmar_senha'] = 'A senha do campo "Confirme a senha" deve ser igual ao campo "Senha"';
        }
    }

    public function excluir()
    {
        $dados = array();
        if (isset($_SESSION['erro_de_validacao'])) {
            $dados['erro_de_validacao'] = $_SESSION['erro_de_validacao'];
            unset($_SESSION['erro_de_validacao']);
        }

        view('login/excluir', $dados);
    }

    public function exclusao_confirmada()
    {
        if (!isset($_POST['senha'])) {
            $_SESSION['erro_de_validacao'] = 'Digite uma senha!';
            header('Location: /excluir-perfil');
            exit;
        }

        $senha = $_POST['senha'];
        
        $login_model = new LoginModel();
        $usuario = $login_model->retorna_usuario($_SESSION['id_usuario']);
        if ($login_model->verificar_login($usuario, $senha)) {
            $resultado = $login_model->excluir($_SESSION['id_usuario']);
            if ($resultado->status == 'success') {
                header('Location: /');
            } else if ($resultado->status == 'error') {
                Log::alert("Houve um erro na action " . __METHOD__ . " ao tentar atualizar um usuário. O erro produziu a seguinte mensagem: " . $resultado->msg);
                view('sistema/erro-interno');
            }
        } else {
            $_SESSION['erro_de_validacao'] = 'A senha digitada está incorreta, tente novamente!';
            header('Location: /excluir-perfil');
            exit;
        }
    }
}