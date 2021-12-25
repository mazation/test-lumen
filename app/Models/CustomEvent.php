<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CustomEvent extends Model
{
    use  HasFactory, SoftDeletes;
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "events";
    protected $fillable = [
        'name', 
    ];

    public function organizer() {
       return $this->belongsTo(User::class, 'owner_id');
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function users() {
        return $this->belongsToMany(User::class, 'users_events', 'event_id', 'user_id')->withTimestamps();
    }

}
