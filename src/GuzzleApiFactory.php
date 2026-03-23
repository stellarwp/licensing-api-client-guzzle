<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClientGuzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use StellarWP\LicensingApiClient\Api;
use StellarWP\LicensingApiClient\ApiBuilder;
use StellarWP\LicensingApiClient\Config;

/**
 * Builds the core licensing API client with Guzzle transport dependencies.
 *
 * @note Use this when you don't have a DI Container to build out the dependency tree.
 */
final class GuzzleApiFactory
{
	private Client $httpClient;

	public function __construct(Client $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	public function make(Config $config): Api
	{
		$psr17 = new HttpFactory();

		return (new ApiBuilder(
			$this->httpClient,
			$psr17,
			$psr17,
			$config
		))->build();
	}
}
