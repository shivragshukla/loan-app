<?php

namespace App\Models;

class PenaltyStatus
{
     /**
     * Statuses.
     */
    public const OPEN_KEY   = '0';
    public const OPEN       = 'OPEN';
    public const CLOSED_KEY = '1';
    public const CLOSED     = 'CLOSED';

    /**
     * List of statuses.
     *
     * @var array
     */
    public static $list = [
        self::OPEN_KEY   => self::OPEN,
        self::CLOSED_KEY => self::CLOSED,
    ];
}
