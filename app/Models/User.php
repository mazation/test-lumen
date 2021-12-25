<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CustomEvent;
use App\Models\Permission;
use App\Models\Role;
use Log;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'api_token', 'password'
    ];

    public function events() {
        return $this->belongsToMany(CustomEvent::class, 'users_events', 'user_id', 'event_id')->withTimestamps();
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'roles_users', 'user_id', 'role_id')->withTimestamps();
    }

    public function permissions() {
        $permission_ids = [];
        $roles = $this->roles()->get();
        foreach ($roles as $role) {
            $permissions = Role::find($role->id)->permissions()->get();
            foreach ($permissions as $permission) {
                array_push($permission_ids, $permission->id);
            }
        }
        return Permission::whereIn('id', $permission_ids);
    }
    
    public function hasPermission(String $permission) {
        $found_permissions = $this->permissions()->where('name', $permission)->get();
        return $found_permissions->isEmpty() ? false : true;
    }

}
