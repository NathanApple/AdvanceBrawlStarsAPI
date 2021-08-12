<?php

namespace App\Console\Commands;

use App\Jobs\StorePlayerFriendlyBattlelogJob;
use App\Models\Player;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SavePlayersBattlelog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'battlelog:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save All Players battlelog';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $players = Player::select('tag')->get();
        foreach ($players as $player) {
            Log::debug("Tag : $player");
            $storePlayer = (new StorePlayerFriendlyBattlelogJob($player['tag']))->onQueue('low');
            dispatch($storePlayer);
            # code...
        }
        // Log::info('Cronjob berhasil dijalankan');
    }
}
