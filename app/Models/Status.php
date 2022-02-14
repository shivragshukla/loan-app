<?php

namespace App\Models;

class Status
{
     /**
     * Statuses.
     */
    public const PENDING_KEY  = '0';
    public const PENDING      = 'PENDING';
    public const APPROVED_KEY = '1';
    public const APPROVED     = 'APPROVED';
    public const REJECTED_KEY = '2';
    public const REJECTED     = 'REJECTED';
    public const CLOSED_KEY   = '3';
    public const CLOSED       = 'CLOSED';

    /**
     * List of statuses.
     *
     * @var array
     */
    public static $list = [
        self::PENDING_KEY  => self::PENDING,
        self::APPROVED_KEY => self::APPROVED,
        self::REJECTED_KEY => self::REJECTED,
        self::CLOSED_KEY   => self::CLOSED,
    ];
}
