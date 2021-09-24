<?php

namespace Itsjeffro\Panel\Fields;

class BelongsTo extends Field
{
    /**
     * {@inheritdoc}
     */
    public $isRelationshipField = true;

    /**
     * {@inheritdoc}
     */
    public $component = 'BelongsTo';

    /**
     * {@inheritdoc}
     */
    public $visibility = [
      self::SHOW_ON_UPDATE,
      self::SHOW_ON_CREATE,
      self::SHOW_ON_DETAIL,
      self::SHOW_ON_INDEX,
    ];
}
