<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
  use HasFactory;

  protected $hidden = ['created_at', 'updated_at'];

  protected $fillable = [
    'title',
    'description',
    'author',
    'isbn',
    'price'
  ];

  public static $rules = [
    'title' => 'required|string',
    'description' => 'required|string|min:10',
    'author' => 'required|string',
    'isbn' => 'required|string|unique:books|min:13|max:13',
    'price' => 'required|numeric|min:0',
  ];

  public static $messages = [];
}