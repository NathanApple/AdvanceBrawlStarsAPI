<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    //
    protected $fillable = ['battle_id', 'result'];

    public $timestamps = false;

    public function battle(){
        return $this->belongsTo(Battle::class, 'battle_id');
    }

    public function team_player(){
        return $this->hasMany(TeamPlayer::class, 'team_id');
    }

    protected $with = array('team_player');

}
