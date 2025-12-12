<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todolist extends Model
{
    /** @use HasFactory<\Database\Factories\TodolistFactory> */
    use HasFactory;
    use SoftDeletes;
    
    protected $guarded = [];
}
