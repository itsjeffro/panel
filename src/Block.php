<?php

namespace Itsjeffro\Panel;

use Illuminate\Support\Collection;
use Itsjeffro\Panel\Fields\Field;

class Block
{
    /**
     * The layout block name.
     *
     * @var string
     */
    private $blockName;

    /**
     * The fields assigned to the block.
     *
     * @var Field[]
     */
    private $fields;

    /**
     * Block constructor.
     */
    public function __construct(string $blockName, array $fields)
    {
        $this->blockName = $blockName;
        $this->fields = $fields;
    }

    /**
     * Returns block's title name.
     */
    public function getName(): string
    {
        return $this->blockName;
    }

    /**
     * Returns block's fields.
     */
    public function fields(): Collection
    {
        return collect($this->fields)->map(function ($field) {
            return $field->setBlockName($this->blockName);
        });
    }
}
