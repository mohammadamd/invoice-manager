<?php

namespace App\Enums;


/**
 * Class BaseEnum
 * @package App\Enums
 */
class BaseEnum
{
    /**
     * return all constants of the enum class
     * @return array
     */
    public static function getConstants()
    {
        return (new \ReflectionClass(static::class))->getConstants();
    }
}
