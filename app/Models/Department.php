<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['faculty_id', 'name', 'code', 'degree'];

    public function faculty() { return $this->belongsTo(Faculty::class); }
    public function etheses() { return $this->hasMany(Ethesis::class); }
}
