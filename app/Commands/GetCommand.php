<?php

namespace App\Commands;

use App\Kube;
use Illuminate\Console\Scheduling\Schedule;
use InvalidArgumentException;
use LaravelZero\Framework\Commands\Command;

class GetCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'get {name} {data}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get a single secret';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = (new Kube)->call('get secret '.$this->argument('name'));
        $secret = $data['data'][$this->argument('data')] ?? null;

        if (! $secret) {
            throw new InvalidArgumentException("Invalid data name specified");
        }

        $this->line(base64_decode($secret));
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
