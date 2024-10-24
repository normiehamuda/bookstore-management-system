<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Book", 
 *     title="Book",
 *     description="A book model"
 * )
 */
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

  /**
   * @OA\Property(property="id", type="integer", description="Book ID")
   */
  protected $id;

  /**
   * @OA\Property(property="title", type="string", description="Book Title")
   */
  protected $title;

  /**
   * @OA\Property(property="author", type="string", description="Book Author")
   */
  protected $author;

  /**
   * @OA\Property(property="description", type="string", description="Book Description")
   */
  protected $description;

  /**
   * @OA\Property(property="isbn", type="string", description="Book ISBN")
   */
  protected $isbn;

  /**
   * @OA\Property(property="price", type="number", format="float", description="Book Price")
   */
  protected $price;
}