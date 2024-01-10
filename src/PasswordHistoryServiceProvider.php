<?php

namespace Imanghafoori\PasswordHistory;

use Illuminate\Support\ServiceProvider;
use Imanghafoori\PasswordHistory\Observers\UserObserver;
use Imanghafoori\PasswordHistory\Facades\PasswordHistoryManager;

class PasswordHistoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        PasswordHistoryManager::shouldProxyTo(PasswordHistory::class);
        $this->mergeConfig();
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/lang/en/passwordHistory.php' => lang_path('en/passwordHistory.php'),
                __DIR__ .'/config/password_history.php' => config_path('password_history.php'),
                __DIR__ .'/Database/migrations/2018_04_08_033256_create_password_histories_table.php' => database_path('migrations/2018_04_08_033256_create_password_histories_table.php'),
            ]);
            
            // commenting out for now as want to make the migration easily editable as laravel switched
            // from unsigned integer to big unsigned integer and things will break without doing some reflection
            // $this->setMigrationFolder();
        }

        $this->listenForModelChanges();
    }

    private function listenForModelChanges()
    {
        $userModels = array_keys(config('password_history.models'));

        foreach ($userModels as $userModel) {
            $userModel::observe(UserObserver::class);
        }
    }

    private function setMigrationFolder()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
    }

    private function mergeConfig()
    {
        $configFile = __DIR__.'/config/password_history.php';

        /*if($this->app->runningUnitTests()) {
            $configFile = __DIR__.'/../tests/Requirements/config/password_history.php';
        }*/

        $this->mergeConfigFrom($configFile, 'password_history');
    }
}
