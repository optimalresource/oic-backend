<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Gig extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "creator",
        "min_salary",
        "max_salary",
        "role",
        "company",
        "country",
        "state",
        "address",
    ];

    public function creator_info() {
        return $this->belongsTo(User::class, 'creator', 'id');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class, 'gig_id');
    }

    // this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();

        static::deleting(function($gig) { // before delete() method call this
             $gig->tags()->delete();
             // do the rest of the cleanup...
        });
    }
}
