<?php

namespace Lacodix\LaravelPlans\Models\Traits;

trait HasCountableAndUncountableFeatures
{
    /**
     * @return array<string, int>
     */
    public function getFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->toArray();
    }

    /**
     * @return array<int, string>
     */
    public function getUncountableFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->filter(static fn (?int $value) => $value === -2)
            ->keys()
            ->toArray();
    }

    /**
     * @return array<string, int>
     */
    public function getCountableFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->filter(static fn (?int $value) => $value >= -1)
            ->toArray();
    }
}
