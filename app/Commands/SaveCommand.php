<?php

namespace App\Commands;

use App\Exceptions\KubeCallFailedException;
use App\Kube;
use Illuminate\Console\Scheduling\Schedule;
use InvalidArgumentException;
use LaravelZero\Framework\Commands\Command;

class SaveCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'save {secretName} {dataName}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create or update a secret';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $secretName = $this->argument('secretName');

        try {
            $secret = (new Kube)->call('get secret '.$secretName)->toArray();
        } catch (KubeCallFailedException $e) {
            $secret = [
                'apiVersion' => 'v1',
                'kind' => 'Secret',
                'metadata' => [
                    'name' => $secretName
                ],
                'type' => 'Opaque',
                'data' => []
            ];
        }

        $secret['data'][$this->argument('dataName')] = base64_encode(
            $this->ask('Enter value for '.$this->argument('dataName'))
        );

        $json = json_encode($secret, JSON_PRETTY_PRINT);

        $this->info('The following secret will be saved');

        $this->line($json);

        if (! $this->confirm('Do you wish to continue?')) {
            return;
        }

        $temp = tempnam(sys_get_temp_dir(), 'kubesecret');

        file_put_contents($temp, $json);

        $response = (new Kube)->call("apply -f {$temp}");

        unlink($temp);

        $this->info('Secret saved');
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
