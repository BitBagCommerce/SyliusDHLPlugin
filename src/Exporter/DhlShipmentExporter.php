<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\Exporter;

use BitBag\SyliusDhlPlugin\Api\DhlApiClientInterface;
use BitBag\SyliusDhlPlugin\Api\WebClientInterface;
use BitBag\SyliusDhlPlugin\Provider\DhlTokenProviderInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use Webmozart\Assert\Assert;

final class DhlShipmentExporter implements DhlShipmentExporterInterface
{
    public const DHL_GATEWAY_CODE = 'dhl';

    public function __construct(
        private WebClientInterface $webClient,
        private DhlTokenProviderInterface $dhlTokenProvider,
        private DhlApiClientInterface $dhlApiClient,
    ) {
    }

    public function export(ShippingExportInterface $shippingExport): void
    {
        $shippingGateway = $shippingExport->getShippingGateway();
        Assert::notNull($shippingGateway);

        if (self::DHL_GATEWAY_CODE !== $shippingGateway->getCode()) {
            return;
        }

        $shipment = $shippingExport->getShipment();
        Assert::notNull($shipment);

        $this->webClient->setShippingGateway($shippingGateway);
        $this->webClient->setShipment($shipment);

        $accessToken = $this->dhlTokenProvider->getAccessToken($shippingGateway);
        Assert::string($accessToken);

        $response = $this->dhlApiClient->exportShipments($shippingGateway, $this->webClient, $accessToken);
    }
}
