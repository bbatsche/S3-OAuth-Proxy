<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Cohort extends Model
{
    public function students()
    {
        return $this->hasMany('App\User');
    }
}
