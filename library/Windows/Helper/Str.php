<?php

namespace ipl\Orm;

class Str
{
    public static function camel($str)
    {
        $normalized = str_replace(['-', '_'], ' ', $str);

        if ($normalized === $str) {
            return $str;
        }

        return lcfirst(str_replace(' ', '', ucwords(strtolower($normalized))));
    }
}
