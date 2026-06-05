<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bundle extends Model
{
    protected $fillable = ['name', 'comment'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
