<?php

namespace Itsjeffro\Panel\Fields;

abstract class Field
{
    /**
     * @param string $name
     * @param string $column
     * @return FieldBuilder
     */
    public static function make(string $name = '', string $column = '')
    {
        $name = empty($name) ? get_called_class() : $name;

        return (new FieldBuilder($name, $column));
    }
}
