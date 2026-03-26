<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'statuses';

    protected $fillable = ['status_name', 'color', 'user_id'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
