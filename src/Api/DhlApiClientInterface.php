<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\Api;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface DhlApiClientInterface
{
    public function getAccessToken(ShippingGatewayInterface $shippingGateway): string;

    public function exportShipments(
        ShippingGatewayInterface $shippingGateway,
        WebClientInterface $webClient,
        string $accessToken,
    ): ResponseInterface;
}
