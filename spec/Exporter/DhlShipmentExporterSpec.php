<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusDhlPlugin\Exporter;

use BitBag\SyliusDhlPlugin\Api\DhlApiClientInterface;
use BitBag\SyliusDhlPlugin\Api\WebClientInterface;
use BitBag\SyliusDhlPlugin\Exporter\DhlShipmentExporter;
use BitBag\SyliusDhlPlugin\Exporter\DhlShipmentExporterInterface;
use BitBag\SyliusDhlPlugin\Provider\DhlTokenProviderInterface;
use BitBag\SyliusDhlPlugin\Resolver\DhlApiUrlResolverInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class DhlShipmentExporterSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DhlShipmentExporter::class);
        $this->shouldHaveType(DhlShipmentExporterInterface::class);
    }

    public function let(
        WebClientInterface $webClient,
        DhlTokenProviderInterface $dhlTokenProvider,
        DhlApiClientInterface $dhlApiClient,
    ): void {
        $this->beConstructedWith(
            $webClient,
            $dhlTokenProvider,
            $dhlApiClient,
        );
    }

    public function it_export_shipment(
        WebClientInterface $webClient,
        ShippingExportInterface $shippingExport,
        ShipmentInterface $shipment,
        ShippingGatewayInterface $shippingGateway,
        RequestStack $requestStack,
        DhlTokenProviderInterface $dhlTokenProvider,
        DhlApiClientInterface $dhlApiClient,
        DhlApiUrlResolverInterface $apiUrlResolver,
        ResponseInterface $response,
    ): void {
        $shippingExport->getShippingGateway()->willReturn($shippingGateway);

        $shippingGateway->getCode()->willReturn(DhlShipmentExporter::DHL_GATEWAY_CODE);

        $shippingExport->getShipment()->willReturn($shipment);

        $webClient->setShippingGateway($shippingGateway)->shouldBeCalled();
        $webClient->setShipment($shipment)->shouldBeCalled();

        $dhlTokenProvider->getAccessToken($shippingGateway)->willReturn('example-access-token');
        $apiUrlResolver->resolve($shippingGateway)->willReturn('https://sandbox.url.com');

        $webClient->getRefNumber()->willReturn('Order 203');
        $webClient->getShipper()->willReturn([
        ]);
        $webClient->getConsignee()->willReturn([]);
        $webClient->getDetails()->willReturn([]);

        $dhlApiClient->exportShipments($shippingGateway, $webClient, 'example-access-token')->willReturn($response);

        $this->export($shippingExport);
    }
}
