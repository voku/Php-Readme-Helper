<?php

declare(strict_types=1);

namespace voku\tests;

enum DummyEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';

    /**
     * Get the label for the enum case.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            DummyEnum::ACTIVE => 'Active',
            DummyEnum::INACTIVE => 'Inactive',
            DummyEnum::PENDING => 'Pending',
        };
    }
}
