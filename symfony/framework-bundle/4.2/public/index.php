<?php

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Env\Env;
use Symfony\Component\Env\EnvFile;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

// The check is to ensure we don't use .env if APP_ENV is defined
if (!Env::has('APP_ENV')) {
    (new EnvFile())->load(__DIR__.'/../.env');
}

$env = Env::get('APP_ENV') ?: 'dev';
$debug = (bool) Env::get('APP_DEBUG') ?: ('prod' !== $env));

if ($debug) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = Env::get('TRUSTED_PROXIES') ?: false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = Env::get('TRUSTED_HOSTS') ?: false) {
    Request::setTrustedHosts(explode(',', $trustedHosts));
}

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
