<?php

namespace local_order;

class helper
{
    public static function convert_to_float($value)
    {
        $value = str_replace('$', '', $value);
        $value = str_replace(',', '', $value);
        return (float)$value;
    }
}