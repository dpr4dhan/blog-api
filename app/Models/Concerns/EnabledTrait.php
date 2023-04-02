<?php
declare(strict_types=1);

namespace App\Models\Concerns;

/**
 * Trait EnabledTrait.
 */
trait EnabledTrait
{
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled',true);
    }
}
