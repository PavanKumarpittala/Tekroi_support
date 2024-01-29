<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Birhtday extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'employees_birthday';
    
    protected $fillable = [
        'ename',
        'eid',
        'edob',
        'erole',
    ];
}