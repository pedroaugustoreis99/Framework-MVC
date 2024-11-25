<?php

namespace helpers;

class Log
{
    // Caminho para o arquivo de log
    private static $logFile = 'app/logs/app.log';

    // Método privado para escrever no log
    private static function writeLog($level, $message)
    {
        // Formata a data e hora
        $timestamp = date('Y-m-d H:i:s');

        // Monta a mensagem de log
        $formattedMessage = sprintf("[%s] %s: %s%s", $timestamp, strtoupper($level), $message, PHP_EOL);

        // Verifica se o diretório existe, senão cria
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // Escreve a mensagem no arquivo de log
        file_put_contents(self::$logFile, $formattedMessage, FILE_APPEND);
    }

    /*
     * Registra uma mensagem de log de nível 'info'.
     * @param string $msg - A mensagem a ser registrada.
     */
    public static function info($msg)
    {
        self::writeLog('info', $msg);
    }

    /*
     * Registra uma mensagem de log de nível 'notice'.
     * @param string $msg - A mensagem a ser registrada.
     */
    public static function notice($msg)
    {
        self::writeLog('notice', $msg);
    }

    /*
     * Registra uma mensagem de log de nível 'warning'.
     * @param string $msg - A mensagem a ser registrada.
     */
    public static function warning($msg)
    {
        self::writeLog('warning', $msg);
    }

    /*
     * Registra uma mensagem de log de nível 'error'.
     * @param string $msg - A mensagem a ser registrada.
     */
    public static function error($msg)
    {
        self::writeLog('error', $msg);
    }

    /*
     * Registra uma mensagem de log de nível 'critical'.
     * @param string $msg - A mensagem a ser registrada.
     */
    public static function critical($msg)
    {
        self::writeLog('critical', $msg);
    }

    /*
     * Registra uma mensagem de log de nível 'alert'.
     * @param string $msg - A mensagem a ser registrada.
     */
    public static function alert($msg)
    {
        self::writeLog('alert', $msg);
    }

    /*
     * Registra uma mensagem de log de nível 'emergency'.
     * @param string $msg - A mensagem a ser registrada.
     */
    public static function emergency($msg)
    {
        self::writeLog('emergency', $msg);
    }
}
