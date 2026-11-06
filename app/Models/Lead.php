<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['source', 'name', 'phone', 'email', 'city', 'interest', 'level', 'country', 'intake', 'consent'])]
class Lead extends Model
{
    public const SOURCE_CALLBACK = 'callback';

    public const SOURCE_COUNSELLING = 'counselling';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'consent' => 'boolean',
        ];
    }
}
