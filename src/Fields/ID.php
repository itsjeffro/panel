<?php

namespace Itsjeffro\Panel\Fields;

class ID extends Field
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
    ];
}
