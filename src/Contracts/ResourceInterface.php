<?php

namespace Itsjeffro\Panel\Contracts;

interface ResourceInterface
{
    public function fields(): array;

    public function modelName(): string;

    public function modelPluralName(): string;
}
