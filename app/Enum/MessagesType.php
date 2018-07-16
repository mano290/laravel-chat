<?php namespace App\Enum;

/**
 * Class MessagesType
 * @package App\Enum
 */
abstract class MessagesType
{
    /**
     * Tipo arquivos para download
     */
    const FILE = "file";

    /**
     * Tipo para visualizacao de imagens
     */
    const IMAGE = "image";

    /**
     * Tipo para mensagens normal
     */
    const MESSAGE = "message";

    /**
     * Tipo para mensagens informacoes do sistema
     */
    const INFO = "info";
}