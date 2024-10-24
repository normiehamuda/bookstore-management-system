<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

  public function messages(): array
  {
    return [
      'isbn.unique' => 'The ISBN must be unique.'
    ];
  }
}