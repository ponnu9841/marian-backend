<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

trait HasUuidsAndHiddenTimestamps
{
    use HasFactory;
    use HasUuids;

    /**
     * Boot the trait to set hidden timestamps and enable UUIDs.
     */
    protected static function bootHasUuidsAndHiddenTimestamps()
    {
        static::retrieved(function ($model) {
            if (empty($model->hidden)) {
                $model->makeHidden(['created_at', 'updated_at']);
            }
        });
    }
}
