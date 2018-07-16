<?php namespace App\Enum;

/**
 * Class RoomType
 * @package App\Enum
 */
abstract class RoomType
{
    /**
     * Tipo de sala CHAT
     * para 2 pessoas
     */
    const ROOM_CHAT = "chat";

    /**
     * Tipo sala Group
     * para 2+ pessoas
     */
    const ROOM_GROUP = "group";

    /**
     * Tipo do usuario da sala
     * 1 admin criador da sala
     */
    const USER_ADMIN = "1";

    /**
     * Tipo do usuario da sala
     * 0 usuario participante
     */
    const USER_NORMAL = "0";
}