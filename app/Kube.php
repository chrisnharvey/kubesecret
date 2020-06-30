<?php

namespace App;

use App\Exceptions\KubeCallFailedException;

class Kube
{
    public function all()
    {

    }

    public function call(string $cmd)
    {
        $json = shell_exec("kubectl {$cmd} -o json");

        if (! $data = json_decode($json, true)) {
            throw new KubeCallFailedException($data);
        }

        return collect($data);
    }
}