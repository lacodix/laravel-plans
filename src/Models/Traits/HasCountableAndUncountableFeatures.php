<?php

namespace Lacodix\LaravelPlans\Models\Traits;

trait HasCountableAndUncountableFeatures
{
    public function getFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->toArray();
    }

    public function getUncountableFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->filter(static fn (?int $value) => $value === -2)
            ->keys()
            ->toArray();
    }

    public function getCountableFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->filter(static fn (?int $value) => $value >= -1)
            ->toArray();
    }
}
