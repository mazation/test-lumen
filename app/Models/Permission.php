<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Role;

class Permission extends Model
{
    use  HasFactory;
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function roles() {
        return $this->belongsToMany(Role::class, 'roles_pemissions');
    }

}
