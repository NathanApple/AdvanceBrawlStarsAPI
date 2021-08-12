<?php

namespace App\Http\Controllers;

use App\Jobs\SearchPlayerByBrawlerJob;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class PlayerController extends Controller
{
    //
    public function save(Request $request){
        $tag = urlencode($request->tag);
        $endpoint = env("BRAWL_API_ENDPOINT", "https://api.brawlstars.com/v1")."/players/$tag";
        $token = env("BRAWL_API_TOKEN", "");

        $response = Http::withHeaders([
            "Authorization" => "Bearer $token"
        ])->get($endpoint)->json();

        Log::debug($request->tag);

        DB::table('players')->updateOrInsert(
                        [
                            "tag" => $response["tag"],
                        ],
                        [
                            "name" => $response["name"], 
                            "club_name" => $response["club"]["name"] ?? '', 
                        ]
                    );

        // $player = Player::create([
        //     "tag" => $response["tag"],
        //     "name" => $response["name"],
        // ]);

        return response()->json($response);
    }

    public function searchRegion(Request $request){
        // $tag = urlencode($request->tag);

        $endpoint = env("BRAWL_API_ENDPOINT", "https://api.brawlstars.com/v1")."/rankings/$request->countryCode/players";
        $token = env("BRAWL_API_TOKEN", "");

        $response = Http::withHeaders([
            "Authorization" => "Bearer $token"
        ])->get($endpoint)->json()['items'];

        $players = [];
        $name = $request->name;
        $index = 0;
        foreach ($response as $player ) {
            if ($player['name'] == $name) {
                array_push($players, $player);
            }
        }
        return response()->json($players);
    }
    
    public function searchBrawler(Request $request){

        $countryCode = $request->countryCode;
        $name = $request->name;
        $endpoint = env("BRAWL_API_ENDPOINT", "https://api.brawlstars.com/v1")."/brawlers";
        $token = env("BRAWL_API_TOKEN", "");
        $response = Http::withHeaders([
            "Authorization" => "Bearer $token"
        ])->get($endpoint)->json()['items'];

        foreach ($response as $brawler) {
            # code...
            $searchJob = (new SearchPlayerByBrawlerJob($countryCode, $brawler['id'], $name))->onQueue('high');
            dispatch($searchJob);
        }

        // $params = $request->all();
        // SearchPlayerByBrawlerJob::dispatch($params);

        return response()->json(['Processing']);
    }
}
