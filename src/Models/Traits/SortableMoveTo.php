<?php

namespace Lacodix\LaravelPlans\Models\Traits;

use Spatie\EloquentSortable\SortableTrait;

trait SortableMoveTo
{
    use SortableTrait {
        moveToStart as baseToStartTrait;
    }

    public function moveTo(int $order): static
    {
        if ($order >= $this->getHighestOrderNumber()) {
            return $this->moveToEnd();
        }

        if ($order <= $this->getLowestOrderNumber()) {
            return $this->moveToStart();
        }

        $orderColumnName = $this->determineOrderColumnName();
        if ($this->$orderColumnName === $order) {
            return $this;
        }

        $oldOrder = $this->$orderColumnName;

        $this->$orderColumnName = $order;
        $this->save();

        if ($order < $oldOrder) {
            $this->buildSortQuery()->where($this->getQualifiedKeyName(), '!=', $this->getKey())
                ->where($orderColumnName, '<', $oldOrder)
                ->where($orderColumnName, '>=', $order)
                ->increment($orderColumnName);
        }

        if ($order > $oldOrder) {
            $this->buildSortQuery()->where($this->getQualifiedKeyName(), '!=', $this->getKey())
                ->where($orderColumnName, '>', $oldOrder)
                ->where($orderColumnName, '<=', $order)
                ->decrement($orderColumnName);
        }

        return $this;
    }

    public function moveToStart(): static
    {
        $firstModel = $this->buildSortQuery()->limit(1)
            ->ordered()
            ->first();

        if ($firstModel->getKey() === $this->getKey()) {
            return $this;
        }

        $orderColumnName = $this->determineOrderColumnName();

        $oldOrder = $this->$orderColumnName;
        $this->$orderColumnName = $firstModel->$orderColumnName;
        $this->save();

        $this->buildSortQuery()
            ->where($this->getQualifiedKeyName(), '!=', $this->getKey())
            ->where($orderColumnName, '<', $oldOrder)
            ->increment($orderColumnName);

        return $this;
    }
}
