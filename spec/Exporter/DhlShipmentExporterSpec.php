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
use BitBag\SyliusDhlPlugin\Manager\ShippingLabelManagerInterface;
use BitBag\SyliusDhlPlugin\Provider\DhlTokenProviderInterface;
use BitBag\SyliusDhlPlugin\Resolver\DhlApiUrlResolverInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;
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
        ShippingLabelManagerInterface $shippingLabelManager,
        ObjectManager $shippingExportManager,
        Registry $registry,
    ): void {
        $this->beConstructedWith(
            $webClient,
            $dhlTokenProvider,
            $dhlApiClient,
            $shippingLabelManager,
            $shippingExportManager,
            $registry,
        );
    }

    public function it_export_shipment(
        WebClientInterface $webClient,
        ShippingExportInterface $shippingExport,
        ShipmentInterface $shipment,
        ShippingGatewayInterface $shippingGateway,
        DhlTokenProviderInterface $dhlTokenProvider,
        DhlApiClientInterface $dhlApiClient,
        DhlApiUrlResolverInterface $apiUrlResolver,
        ResponseInterface $response,
        ShippingLabelManagerInterface $shippingLabelManager,
        ObjectManager $shippingExportManager,
        Registry $registry,
        WorkflowInterface $workflow,
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
        $response->toArray()->willReturn(
            [
            'items' => [['label' => ['b64' => 'base64-encoded-pdf', 'fileFormat' => 'pdf'], 'shipmentNo' => '33333']]],
        )->shouldBeCalled();

        $shippingLabelManager->saveShippingLabel($shippingExport, 'base64-encoded-pdf', 'pdf')
            ->shouldBeCalled();

        $shippingExport->setState(ShippingExportInterface::STATE_EXPORTED)->shouldBeCalled();
        $shippingExport->setExportedAt(Argument::type(DateTime::class))->shouldBeCalled();
        $shippingExport->setExternalId('33333')->shouldBeCalled();

        $registry->get($shipment, ShipmentTransitions::GRAPH)->willReturn($workflow);
        $workflow->can($shipment, ShipmentTransitions::TRANSITION_SHIP)->willReturn(true);
        $workflow->apply($shipment, ShipmentTransitions::TRANSITION_SHIP)->shouldBeCalled();

        $shippingExportManager->persist($shippingExport)->shouldBeCalled();
        $shippingExportManager->flush()->shouldBeCalled();

        $this->export($shippingExport);
    }
}
