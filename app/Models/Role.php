<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Role",
 *     title="Role",
 *     description="A role model"
 * )
 */
class Role extends Model
{
  /**
   * @OA\Property(property="id", type="integer", description="Role ID")
   */
  protected $id;

  /**
   * @OA\Property(property="name", type="string", description="Role name (e.g., Admin, User)")
   */
  protected $name;
}