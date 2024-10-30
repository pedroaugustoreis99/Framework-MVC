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
        extract($_POST);

        if (!isset($usuario) OR !isset($senha) OR !isset($confirmar_senha)) {
            Log::critical('O usuário realizou uma requisição via POST para a Action ' . __METHOD__ . ' com dados incompletos. Está faltando $_POST["usuario"] ou $_POST["senha"] ou $_POST["confirmar_senha"]');
            view('sistema/erro-interno');
            exit;
        } 

        $this->validar_campos($usuario, $senha, $confirmar_senha, LoginController::STATUS_CRIANDO);
        if (!empty($this->erros)) {
            $_SESSION['erros_de_validacao'] = $this->erros;
            header('Location: /cadastrar-usuario');
            exit;
        }

        $login_model = new LoginModel();
        $resultado = $login_model->cadastrar($usuario, $senha);
        if ($resultado->status == 'success') {
            /*
             * Depois vou criar uma view informando que o usuário foi cadastrado com sucesso!
             */
            echo 'Usuário cadastrado com sucesso!';
        } else if ($resultado->status == 'error') {
            Log::alert("Houve um erro na action " . __METHOD__ . " ao tentar cadastrar um usuário. O erro produziu a seguinte mensagem: " . $resultado->msg);
            view('sistema/erro-interno');
        }
        
    }

    /*
     * Métodos auxiliares que serão utilizados nas Actions
     */
    private function validar_campos($usuario, $senha, $confirmar_senha, $status_operacao, $id = null)
    {
        $login_model = new LoginModel();

        /*
         * Verificando se os dados vão ser utilizados para criar ou atualizar um usuário
         */
        if ($status_operacao == LoginController::STATUS_CRIANDO) {
            $usuario_existe = $login_model->usuario_existe_criando($usuario);
        } else if ($status_operacao == LoginController::STATUS_ATUALIZANDO) {
            $usuario_existe = $login_model->usuario_existe_atualizando($id, $usuario);
        } else {
            // Depois tenho que implementar uma lógica para esse caso!
        }
        
        /*
         * Validações do campo usuário
         */
        if (empty($usuario)) {
            $this->erros['usuario'] = "Digite um usuário.";
        } else if (strlen($usuario) < 5 OR strlen($usuario) > 60) {
            $this->erros['usuario'] = "O campo usuário deve ter entre 5 e 60 caracteres.";
        } else if ($usuario_existe) {
            $this->erros['usuario'] = 'O usuário digitado já existe, digite outro usuário.';
        }

        /*
         * Validações do campo senha
         */
        if (empty($senha)) {
            $this->erros['senha'] = 'Digite uma senha.';
        } else if (strlen($senha) < 5 OR strlen($senha) > 60) {
            $this->erros['senha'] = 'O campo senha deve ter entre 5 e 60 caracteres.';
        } else if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/", $senha)) {
            $this->erros['senha'] = 'A senha deve conter pelo menos uma letra maiúscula, pelo menos uma letra minúscula e pelo menos um número.';
        }

        /*
         * Validação do campo confirmar_senha
         */
        if ($confirmar_senha != $senha) {
            $this->erros['confirmar_senha'] = 'A senha do campo "Confirme a senha" deve ser igual ao campo "Senha"';
        }
    }
}