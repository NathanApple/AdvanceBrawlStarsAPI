<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamPlayer extends Model
{
    //
    protected $fillable = ['team_id', 'tag', 'name', 'brawler_name'];

    public $timestamps = false;

    public function team(){
        return $this->belongsTo(Team::class, 'team_id');
    }
}
