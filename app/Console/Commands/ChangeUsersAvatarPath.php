<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChangeUsersAvatarPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:path';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change users avatar path';

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
        $users = DB::table('users')->select('id', 'photo_url')->get();
        foreach ($users as $user){
            if($user->photo_url) {
                $halfUrl = strstr($user->photo_url, "/profiles");
                $url = env('APP_SPACES_PREFIX') . $halfUrl;
                DB::table('users')->where('id', $user->id)->update(array('photo_url' => $url));
            }
        }
    }
}
