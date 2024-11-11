<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts';

    protected $fillable = [
        'start_shift',
        'end_shift',
        'state',
        'current_user',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id');
    }
}
