<?php

namespace Itsjeffro\Panel;

use Itsjeffro\Panel\Fields\Field;

class Block
{
    /**
     * @var string
     */
    private $blockName;

    /**
     * @var Field[]
     */
    private $fields;

    /**
     * @param Field[] $fields
     */
    public function __construct(string $blockName, array $fields)
    {
        $this->blockName = $blockName;
        $this->fields = $fields;
    }

    public function getName(): string
    {
        return $this->blockName;
    }

    public function fields(): array
    {
        return $this->fields;
    }
}
