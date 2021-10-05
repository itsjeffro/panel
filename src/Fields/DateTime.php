<?php

namespace Itsjeffro\Panel\Fields;

class DateTime extends Field
{
    /**
     * {@inheritdoc}
     */
    public $component = 'DateTime';

    /**
     * {@inheritdoc}
     */
    public $visibility = [
        self::SHOW_ON_DETAIL,
        self::SHOW_ON_INDEX,
        self::SHOW_ON_CREATE,
        self::SHOW_ON_UPDATE,
    ];
}
