<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


/**
 * @OA\Schema(
 *     schema="BookRequest", 
 *     title="BookRequest",
 *     description="Request data for creating a book"
 * )
 */
class BookFormRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'title' => 'required',
      'author' => 'required',
      'price' => 'required|numeric|min:0',
      'description' => 'required|min:10',
      'isbn' => [
        'required',
        'min:13',
        'max:13',
        Rule::unique('books')->ignore($this->book),
      ]
    ];
  }

  /**
   * @OA\Property(property="title", type="string", description="Book Title")
   */
  public $title;

  /**
   * @OA\Property(property="author", type="string", description="Book Author")
   */
  public $author;

  /**
   * @OA\Property(property="description", type="string", description="Book Description")
   */
  public $description;

  /**
   * @OA\Property(property="isbn", type="string", description="Book ISBN")
   */
  public $isbn;

  /**
   * @OA\Property(property="price", type="number", format="float", description="Book Price")
   */
  public $price;

  public function messages(): array
  {
    return [
      'isbn.unique' => 'The ISBN must be unique.'
    ];
  }
}