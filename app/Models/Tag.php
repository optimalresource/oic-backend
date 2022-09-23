<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "name",
        "creator",
        "gig_id"
    ];

    public function tags()
    {
        return $this->belongsToMany(Gig::class, 'id', 'gig_id');
    }
}
