<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Driver extends Model
{
    use HasFactory;
    protected  $guarded = [];
    public function user() :BelongsTo
    {
       return $this->belongsTo(User::class);
    }
    public function trips() :BelongsToMany
    {
        return $this->belongsToMany(Trip::class);
    }
}
