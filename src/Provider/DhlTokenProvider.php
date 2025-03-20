<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\Provider;

use BitBag\SyliusDhlPlugin\Resolver\DhlApiUrlResolverInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

final class DhlTokenProvider implements DhlTokenProviderInterface
{
    private const TOKEN_EXPIRATION_TIME = 300;

    public function __construct(
        private HttpClientInterface $client,
        private CacheInterface $cache,
        private DhlApiUrlResolverInterface $apiUrlResolver,
    ) {
    }

    public function getAccessToken(ShippingGatewayInterface $shippingGateway): mixed
    {
        return $this->cache->get('dhl_access_token', function (ItemInterface $item) use ($shippingGateway) {
            $item->expiresAfter(self::TOKEN_EXPIRATION_TIME);

            $response = $this->client->request(
                'POST',
                $this->apiUrlResolver->resolve($shippingGateway) . '/parcel/de/account/auth/ropc/v1/token',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'content-type' => 'application/x-www-form-urlencoded',
                    ],
                    'body' => [
                        'grant_type' => 'password',
                        'username' => $shippingGateway->getConfigValue('username'),
                        'password' => $shippingGateway->getConfigValue('password'),
                        'client_id' => $shippingGateway->getConfigValue('client_id'),
                        'client_secret' => $shippingGateway->getConfigValue('client_secret'),
                    ],
                ],
            );

            $data = $response->toArray();

            $accessToken = $data['access_token'];
            Assert::string($accessToken);

            return $accessToken;
        });
    }
}
