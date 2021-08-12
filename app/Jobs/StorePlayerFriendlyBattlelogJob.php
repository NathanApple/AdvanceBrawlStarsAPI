<?php

namespace App\Jobs;

use App\Models\Battle;
use App\Models\Team;
use App\Models\TeamPlayer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StorePlayerFriendlyBattlelogJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tag;
    public function __construct($tag)
    {
        //
        $this->tag = $tag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $urlTag = urlencode($this->tag);
        $endpoint = env("BRAWL_API_ENDPOINT", "https://api.brawlstars.com/v1")."/players/$urlTag/battlelog";
        $token = env("BRAWL_API_TOKEN", "");

        $response = Http::withHeaders([
            "Authorization" => "Bearer $token"
        ])->get($endpoint)->json()["items"];
        
        $matches = [];
        $key = 0;
        $newtag = $this->tag;
        foreach ($response as $match ){
            if (!isset($match["battle"]["type"])) {
                continue;    
            }
            if ($match["battle"]["type"] == "friendly") {

                if ($match["battle"]["mode"] == 'soloShowdown' || $match["battle"]["mode"] == 'duoShowdown') {
                    continue;
                }

                // Log::debug($match['battleTime']);
                // $battleTime = $match['battleTime'];
                // $battleTime = Carbon::parse($match['battleTime'])->toIso8601String();
                $battleTime = Carbon::parse(substr($match['battleTime'], 0 , 16))->format('Y-m-d H:i:s');

                // Dont declare results variable here

                // $battleTime = $match['battleTime'];
                $battle = Battle::firstOrCreate(
                    ['battle_time' => $battleTime],
                    [
                        'event_id' => $match["event"]["id"],
                        'event_map' => $match["event"]["map"],
                        'mode' => $match["battle"]["mode"],
                        'type' => $match["battle"]["type"],
                        'duration' => $match["battle"]["duration"],
                    ]
                );

                if ($battle->wasRecentlyCreated) {
                    $results = [];

                    $teams = $match['battle']['teams'];
                    $result = $match['battle']['result'];
                    // Check result
                    // Find player Tag in the team to compare with the result
                    // Should array with result for each team 
                    foreach ($teams as $key => $team) {
                        if ($result == 'victory') {

                            $results[$key] = 'defeat';
                            foreach ($team as $x){

                                if ($x['tag'] == $newtag) {
                                    $results[$key] = 'victory';
                                }
                            }
                            
                        } else if ($result == 'defeat'){
                            $results[$key] = 'victory';
                            foreach ($team as $x){

                                if ($x['tag'] == $newtag) {
                                    $results[$key] = 'defeat';
                                }
                            }
                        } else {
                            $results['0'] = "draw";
                            $results['1'] = "draw";
                            
                        }
                          
                    }

                    foreach ($teams as $key => $x){
                        $team = Team::create([
                            'result' => $results[$key],
                            'battle_id' => $battle->id
                        ]);

                        foreach ($x as $y) {
                            TeamPlayer::create([
                                'team_id' => $team->id,
                                'tag' => $y['tag'],
                                'name' => $y['name'],
                                'brawler_name' => $y['brawler']['name'],
                            ]);
                        }
                    }
                }

                // $matches[$match["battleTime"]] = $match;
                Log::debug($match['battleTime']);

            }
        }
    }
}
