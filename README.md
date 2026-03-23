# Licensing API Client Guzzle

> ⚠️ **This is a read-only repository!**
> For pull requests or issues, see [stellarwp/licensing-api-client-monorepo](https://github.com/stellarwp/licensing-api-client-monorepo).

Guzzle transport and factory integration for the StellarWP Licensing API client.

## Installation

Update your composer.json and add the following to your `repositories` object:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:stellarwp/licensing-api-client.git"
        },
        {
            "type": "vcs",
            "url": "git@github.com:stellarwp/licensing-api-client-guzzle.git"
        }
    ]
}
```

Then, install:

```shell
composer require stellarwp/licensing-api-client-guzzle
```

## Examples

For end-to-end API cookbook examples, see:

- [API Examples](https://github.com/stellarwp/licensing-api-client-monorepo/blob/main/docs/examples/index.md)

## Usage

For a DI52 Provider:

```php
<?php declare(strict_types=1);

namespace MyApp\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use StellarWP\LicensingApiClient\Api;
use StellarWP\LicensingApiClient\Config;
use StellarWP\LicensingApiClient\Contracts\LicensingClientInterface;
use StellarWP\LicensingApiClient\Http\RequestExecutor;
use lucatume\DI52\ServiceProvider;

final class LicensingApiProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->container->singleton(Client::class, Client::class);
		$this->container->singleton(HttpFactory::class, HttpFactory::class);

		$this->container->when(RequestExecutor::class)
		                ->needs(ClientInterface::class)
		                ->give(static fn( $c ): ClientInterface => $c->get(Client::class));

		$this->container->bind(
			RequestFactoryInterface::class,
			static fn( $c ): RequestFactoryInterface => $c->get(HttpFactory::class)
		);

		$this->container->bind(
			StreamFactoryInterface::class,
			static fn( $c ): StreamFactoryInterface => $c->get(HttpFactory::class)
		);

		$this->container->singleton(
			Config::class,
			static function (): Config {
				return new Config(
					'https://licensing.example.com',
					null, // Pass a token if you plan to make authenticated requests.
					'my-app/1.0.0' // Your client user agent.
				);
			}
		);

		$this->container->bind(LicensingClientInterface::class, Api::class);
	}
}
```

That lets you resolve the fully-wired core client from the container:

```php
$api = $container->get(LicensingClientInterface::class);
```

Then you can use the resolved client normally:

```php
$catalog = $api->products()->catalog('LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N');

$validation = $api->licenses()->validate(
	'LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N',
	['kadence', 'learndash'],
	'customer-site.com'
);

$trustedApi = $api->withConfiguredToken();
$balances   = $trustedApi->credits()->balance(
	'LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N',
	'customer-site.com'
);
```

For a public or unauthenticated client without a Container:

```php
<?php declare(strict_types=1);

use GuzzleHttp\Client;
use StellarWP\LicensingApiClient\Config;
use StellarWP\LicensingApiClientGuzzle\GuzzleApiFactory;

$api = (new GuzzleApiFactory(
    new Client()
))->make(new Config(
    'https://licensing.example.com',
    null,
    'my-app/1.0.0' // Your client user agent.
));
```

For a trusted source with a configured token:

```php
<?php declare(strict_types=1);

use GuzzleHttp\Client;
use StellarWP\LicensingApiClient\Config;
use StellarWP\LicensingApiClientGuzzle\GuzzleApiFactory;

$api = (new GuzzleApiFactory(
    new Client()
))->make(new Config(
    'https://licensing.example.com',
    'pk_test_your_token_here',
    'portal/1.0.0' // Your client user agent.
));

$trustedApi = $api->withConfiguredToken();
```

## Status

This package is being developed in the monorepo and published as a read-only split repository.
