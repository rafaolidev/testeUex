<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'cpf',
        'lat',
        'long',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zipcode',
        'country',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
