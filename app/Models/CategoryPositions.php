<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryPositions extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'sub_category_id', 'position', 'date'];
}
