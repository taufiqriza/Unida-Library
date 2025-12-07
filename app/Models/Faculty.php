<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = ['name', 'code'];

    public function departments() { return $this->hasMany(Department::class); }
    public function etheses() { return $this->hasManyThrough(Ethesis::class, Department::class); }
}
