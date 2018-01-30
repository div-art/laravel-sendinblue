<?php

namespace DivArt\SendInBlue;

use Illuminate\Mail\MailServiceProvider;
use DivArt\SendInBlue\Transport\SendInBlueTransport;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class SendInBlueServiceProvider extends MailServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/sendinblue.php' => config_path('sendinblue.php'),
        ]);
    }

    function registerSwiftMailer()
    {
        if ($this->app['config']['mail.driver'] == 'sendinblue') {
            $this->registerSendInBlueSwiftMailer();
        } else {
            parent::registerSwiftMailer();
        }
    }

    private function registerSendInBlueSwiftMailer()
    {
        $this->app->singleton('swift.mailer', function ($app) {
            $config = $app->make('config')->get('sendinblue');
            return new \Swift_Mailer(new SendInBlueTransport(
                $this->guzzle($config),
                $config['key']
            ));
        });
    }

    /**
     * Get a fresh Guzzle HTTP client instance.
     *
     * @param  array  $config
     * @return \GuzzleHttp\Client
     */
    protected function guzzle($config)
    {
        return new Client(Arr::add(
            $config['guzzle'] ?? [], 'connect_timeout', 60
        ));
    }
}