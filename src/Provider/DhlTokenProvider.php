<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\Provider;

use BitBag\SyliusDhlPlugin\Api\DhlApiClientInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class DhlTokenProvider implements DhlTokenProviderInterface
{
    private const TOKEN_EXPIRATION_TIME = 300;

    public function __construct(
        private DhlApiClientInterface $dhlApiClient,
        private CacheInterface $cache,
    ) {
    }

    public function getAccessToken(ShippingGatewayInterface $shippingGateway): mixed
    {
        return $this->cache->get('dhl_access_token', function (ItemInterface $item) use ($shippingGateway) {
            $item->expiresAfter(self::TOKEN_EXPIRATION_TIME);

            return $this->dhlApiClient->getAccessToken($shippingGateway);
        });
    }
}
