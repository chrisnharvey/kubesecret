<?php

namespace App\Commands;

use App\Kube;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class AllCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'all';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all Kubernetes secrets';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = (new Kube)->call('get secrets');

        foreach ($data['items'] as $item) {
            $items[] = [
                $item['metadata']['name'],
                $item['type'],
                count($item['data']),
            ];
        }

        $this->table([
            'Name',
            'Type',
            'Data'
        ], $items);
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
