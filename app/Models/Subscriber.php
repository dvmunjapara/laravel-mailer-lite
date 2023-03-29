<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'email', 'country', 'subscribed_at'];

    public function scopeFilterEmail($query, $email) {

        return $query->when($email, function ($query) use ($email) {
            $query->where('email', 'like' , "$email%");
        });
    }
}
