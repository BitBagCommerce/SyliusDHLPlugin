<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\Api;

use BitBag\SyliusDhlPlugin\Resolver\DhlApiUrlResolverInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

final class DhlApiClient implements DhlApiClientInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private DhlApiUrlResolverInterface $apiUrlResolver,
    ) {
    }

    public function exportShipments(
        ShippingGatewayInterface $shippingGateway,
        WebClientInterface $webClient,
        string $accessToken,
    ): ResponseInterface {
        return $this->client->request(
            'POST',
            $this->apiUrlResolver->resolve($shippingGateway) . '/parcel/de/shipping/v2/orders',
            [
                'headers' => [
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => [
                    'shipments' => [
                        [
                            'product' => 'V01PAK',
                            'billingNumber' => '33333333330101',
                            'refNo' => $webClient->getRefNumber(),
                            'shipper' => $webClient->getShipper(),
                            'consignee' => $webClient->getConsignee(),
                            'details' => $webClient->getDetails(),
                        ],
                    ],
                ],
            ],
        );
    }

    public function getAccessToken(ShippingGatewayInterface $shippingGateway): string
    {
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
    }
}
