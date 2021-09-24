<?php

namespace Itsjeffro\Panel\Fields;

class Textarea extends Field
{
    /**
     * @var string
     */
    public $component = 'Textarea';

    /**
     * {@inheritdoc}
     */
    public $visibility = [
        self::SHOW_ON_DETAIL,
        self::SHOW_ON_UPDATE,
        self::SHOW_ON_CREATE,
    ];
}
