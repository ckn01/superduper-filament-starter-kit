<?php

// namespace Shipu\WebInstaller\Utilities;
namespace App\Wizard\Utilities;

use Illuminate\Support\Facades\Artisan;

class EnvironmentHelper
{
    public function putPermanentEnv($key, $value): void
    {
        $path = app()->environmentFilePath();

        $oldValue = env($key);
        $oldValue = preg_match('/\s/', $oldValue) ? "\"{$oldValue}\""
            : $oldValue;
        $escaped = preg_quote('='.$oldValue, '/');
        $value = preg_match('/\s/', $value) ? "\"{$value}\"" : $value;

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
    }

    public function updateAllEnv($installerFormConfig, $environment): void
    {

        $changed = false;

        foreach ($installerFormConfig as $key => $config) {
            $newValue = array_get($environment, $key);
            if ($newValue === null) {
                continue;
            }

            $oldValue = env($config['env_key']);

            if ($oldValue !== $newValue) {
                $changed = true;
                $this->putPermanentEnv($config['env_key'], $newValue);
            }
            // $this->putPermanentEnv($config['env_key'], $newValue);
        }

        if ($changed) {
            Artisan::call('config:clear');
            // $this->clearConfig();
        }
    }
    
}