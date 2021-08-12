<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    //
    protected $fillable = ["battle_time", "event_id",'event_map', 'mode', 'type', 'result', 'duration'];

    protected $attributes = [
        'isBalanced' => false,
    ];

    public function team(){
        return $this->hasMany(Team::class, 'battle_id');
    }

    protected $with = array('team');

    public $timestamps = false;
}
