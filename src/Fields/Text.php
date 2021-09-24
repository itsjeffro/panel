<?php

namespace Itsjeffro\Panel\Fields;

class Text extends Field
{
    /**
     * @var string
     */
    public $component = 'Text';

    /**
     * {@inheritdoc}
     */
    public $visibility = [
        self::SHOW_ON_DETAIL,
        self::SHOW_ON_INDEX,
        self::SHOW_ON_UPDATE,
        self::SHOW_ON_CREATE,
    ];
}
