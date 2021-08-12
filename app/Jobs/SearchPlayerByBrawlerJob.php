<?php

namespace App\Jobs;

use App\Models\Player;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchPlayerByBrawlerJob extends Job
{

    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $countryCode;
    public $brawlerId;
    public $name;

    public function __construct($countryCode, $brawlerId, $name)
    {
        //
        $this->countryCode = $countryCode;
        $this->brawlerId = $brawlerId;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        // $params = $this->params;
        $countryCode = $this->countryCode;
        $brawlerId = $this->brawlerId;
        $name = $this->name;
        $endpoint = env("BRAWL_API_ENDPOINT", "https://api.brawlstars.com/v1")."/rankings/$countryCode/brawlers/$brawlerId";
        $token = env("BRAWL_API_TOKEN", "");
        Log::debug("Processing Jobs : $brawlerId | $countryCode");
        $response = Http::withHeaders([
            "Authorization" => "Bearer $token"
        ])->get($endpoint)->json()['items'];

        
        foreach ($response as $player) {
            # code...
            if ($player['name'] == $name) {
                $player = Player::updateOrInsert(
                    ['tag' => $player['tag']],
                    [
                        'name' => $player['name'],
                        'club_name' => $player["club"]["name"], 
                    ]
                );
                
            }
        }
    }
}
