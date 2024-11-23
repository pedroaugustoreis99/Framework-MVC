<?php

namespace models;

use system\Database;

class LoginModel extends Database
{
    /*
     * Essa função consulta o banco de dados para verificar se o nome de usuário fornecido
     * existe e se a senha está correta. 
     * 
     * @param string $usuario - O nome de usuário fornecido pelo usuário.
     * @param string $senha - A senha fornecida pelo usuário.
     * @return bool - Retorna true se as credenciais estiverem corretas, caso contrário, retorna false.
     */
    public function verificar_login($usuario, $senha)
    {
        $sql = 'SELECT senha FROM usuarios WHERE usuario = AES_ENCRYPT(:usuario, "' . MYSQL_AES_KEY . '")';
        $param = [':usuario' => $usuario];
        $result = $this->execute_query($sql, $param);
        
        if ($result->affected_rows == 0) return false;
        
        return password_verify($senha, $result->results[0]->senha);
    }

    /*
     * Atualiza a data e hora do último login de um usuário no banco de dados.
     */
    public function definir_ultimo_login($usuario)
    {
        $sql = 'UPDATE usuarios SET ultimo_login = NOW() WHERE usuario = AES_ENCRYPT(:usuario, "' . MYSQL_AES_KEY . '")';
        $param = [':usuario' => $usuario];
        $this->execute_non_query($sql, $param);
    }

    /*
     * Método para verificar se um usuário já existe ao criar.
     */
    public function usuario_existe_criando($usuario)
    {
        $sql = 'SELECT id, usuario FROM usuarios WHERE AES_DECRYPT(usuario, "' . MYSQL_AES_KEY . '") = :usuario';
        $params = [':usuario' => $usuario];
        $resultado = $this->execute_query($sql, $params);

        return $resultado->affected_rows != 0;
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

    public function retorna_id($usuario)
    {
        $sql = 'SELECT id FROM usuarios WHERE usuario = AES_ENCRYPT(:usuario, "' . MYSQL_AES_KEY . '")';
        $param = [':usuario' => $usuario];
        return $this->execute_query($sql, $param)->results[0]->id;
    }

    public function retorna_usuario($id)
    {
        $sql = 'SELECT AES_DECRYPT(usuario, "' . MYSQL_AES_KEY . '") as usuario FROM usuarios WHERE id = :id';
        $param = [':id' => $id];
        return $this->execute_query($sql, $param)->results[0]->usuario;
    }

    public function atualizar($id, $usuario, $senha)
    {
        $sql = '
            UPDATE usuarios SET
                usuario = AES_ENCRYPT(:usuario, "' . MYSQL_AES_KEY . '"),
                senha = :senha,
                updated_at = NOW()
            WHERE id = :id
        ';
        $params = [
            ':usuario' => $usuario,
            ':senha' => password_hash($senha, PASSWORD_DEFAULT),
            ':id' => $id
        ];
        return $this->execute_non_query($sql, $params);
    }

    public function excluir($id)
    {
        $sql = 'DELETE FROM usuarios WHERE id = :id';
        $param = [':id' => $id];
        return $this->execute_non_query($sql, $param);
    }
}