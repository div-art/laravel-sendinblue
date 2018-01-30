# Laravel 5.5 SendInBlue mail driver

Extends Laravel mail drivers to send emails using SendInBlue API

## Installation

To install, run the following in your project directory:

``` bash
$ composer require div-art/laravel-sendinblue
```

Then in `config/app.php` add the following to the `providers` array:

```
DivArt\SendInBlue\SendInBlueServiceProvider::class,
```

## Configuration

To publish SendInBlue's configuration file, run the following `vendor:publish` command:

```
php artisan vendor:publish --provider="DivArt\SendInBlue\SendInBlueServiceProvider"
```

This will create sendinblue.php in your configuration directory. In this file you must specify your sendinblue api-key. The api-key can be obtained from the settings of the SendInBlue account. Also SendInBlue should activate your SMTP account. To do this, write to them in support.

`.env`

```
MAIL_DRIVER=sendinblue
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.