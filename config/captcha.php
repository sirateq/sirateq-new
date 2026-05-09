<?php
/*
 * Secret key and Site key get on https://www.google.com/recaptcha
 * */
return [
    'secret' => env('RECAPTCHAV3_SECRET', '6Lf2FrIsAAAAADnI7rLgZr4fJomQnxFvDyD0wbC5'),
    'sitekey' => env('RECAPTCHAV3_SITEKEY', '6Lf2FrIsAAAAAA8eoFatQHQuIV1NeHHHbBhz2jUU'),
    /**
     * @var string|null Default ``null``.
     * Custom with function name (example customRequestCaptcha) or class@method (example \App\CustomRequestCaptcha@custom).
     * Function must be return instance, read more in repo ``https://github.com/thinhbuzz/laravel-google-captcha-examples``
     */
    'request_method' => null,
    'options' => [
        'multiple' => false,
        'lang' => app()->getLocale(),
    ],
    'attributes' => [
        'theme' => 'light'
    ],
];