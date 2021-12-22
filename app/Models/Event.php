<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Event extends Model
{
    use  HasFactory, SoftDeletes;
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

}
