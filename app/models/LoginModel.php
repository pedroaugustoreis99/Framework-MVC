<?php

namespace models;

use system\Database;

class LoginModel extends Database
{
    /*
     * Método para verificar se um usuário já existe ao criar.
     */
    public function usuario_existe_criando($usuario)
    {
        $sql = 'SELECT id, usuario FROM usuarios WHERE AES_DECRYPT(usuario, "' . MYSQL_AES_KEY . '") = :usuario';
        $params = [':usuario' => $usuario];
        $resultado = $this->execute_query($sql, $params);

        if ($resultado->affected_rows == 0) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * Método para verificar se um usuário já existe ao atualizar.
     */
    public function usuario_existe_atualizando($id, $usuario)
    {
        $sql = 'SELECT id, usuario FROM usuarios WHERE id <> :id AND AES_DECRYPT(usuario, "' . MYSQL_AES_KEY . '") = :usuario';
        $params = [
            ':id' => $id,
            ':usuario' => $usuario
        ];
        $resultado = $this->execute_query($sql, $params);

        if ($resultado->affected_rows == 0) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * Método para cadastrar um novo usuário.
     */
    public function cadastrar($usuario, $senha)
    {
        $sql = '
            INSERT INTO usuarios (usuario, senha, created_at) VALUES ( 
                AES_ENCRYPT(:usuario, "' . MYSQL_AES_KEY . '"),
                :senha,
                NOW()
            )
        ';
        $params = [
            ':usuario' => $usuario,
            ':senha' => password_hash($senha, PASSWORD_DEFAULT)
        ];
        return $this->execute_non_query($sql, $params);
    }
}