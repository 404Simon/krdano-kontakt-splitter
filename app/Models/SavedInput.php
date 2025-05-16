<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedInput extends Model
{
    protected $fillable = [
        'salutation',
        'title',
        'gender',
        'firstname',
        'lastname',
        'language',
        'letter_salutation',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(SavedInput::class);
    }
}
