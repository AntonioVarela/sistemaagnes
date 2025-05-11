<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class materia extends Model
{
    //
    public function horario()
    {
        return $this->hasOne(horario::class, 'materia');
    }
}
