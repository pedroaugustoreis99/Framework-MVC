<?php

namespace system;

trait Middlewares
{
    /*
     * Essa propriedade associa um nome de middleware a uma classe de middleware.
     * O array mapeia nomes de middlewares para as classes que os implementam.
     */
    protected static $middleware = [
        'redirecionar_se_autenticado' => 'middlewares\\RedirecionarSeAutenticadoMiddleware',
        /*
         * Responsável por proteger rotas que são acessíveis apenas a usuários logados.
         */
        'verificar_autenticacao' => 'middlewares\\VerificarAutenticacaoMiddleware'
    ];

    /*
     * Essa propriedade associa uma rota específica a uma lista de middlewares que devem ser executados para aquela rota.
     * Cada rota pode ter um ou mais middlewares definidos.
     */
    protected static $rotaMiddleware = [
        '/login' => [
            'redirecionar_se_autenticado'
        ],
        '/cadastrar-usuario' => [
            'redirecionar_se_autenticado'
        ],
        '/login-submit' => [
            'redirecionar_se_autenticado'
        ],
        '/cadastrar-usuario-submit' => [
            'redirecionar_se_autenticado'
        ],
        /*
         * Rotas acessíveis apenas a usuários logados.
         */
        '/home' => [
            'verificar_autenticacao'
        ],
        '/logout' => [
            'verificar_autenticacao'
        ],
        '/editar-perfil' => [
            'verificar_autenticacao'
        ],
        '/excluir-perfil' => [
            'verificar_autenticacao'
        ],
        '/atualizar-perfil' => [
            'verificar_autenticacao'
        ],
        '/exclusao-confirmada' => [
            'verificar_autenticacao'
        ],
    ];

    /*
     * Executa os middlewares associados a uma rota específica.
     * 
     * @param string $rota - A rota para a qual os middlewares devem ser executados.
     */
    protected static function executarMiddleware($rota)
    {
        /*
         * Verifica se existem middlewares associados à rota especificada
         */
        if (key_exists($rota, self::$rotaMiddleware)) {
            /*
             * Percorre todos os middlewares associados à rota
             */
            foreach (self::$rotaMiddleware[$rota] as $middleware) {
                /*
                 * Executa o método handle() do middleware.
                 */
                $middlewareClass = self::$middleware[$middleware];
                $middlewareObject = new $middlewareClass();
                $middlewareObject->handle();
            }
        }
    }
}