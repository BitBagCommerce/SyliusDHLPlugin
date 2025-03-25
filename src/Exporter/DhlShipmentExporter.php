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
use BitBag\SyliusDhlPlugin\Storage\ShippingLabelStorageInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Workflow\Registry;
use Webmozart\Assert\Assert;

final class DhlShipmentExporter implements DhlShipmentExporterInterface
{
    public const DHL_GATEWAY_CODE = 'dhl';

    public function __construct(
        private WebClientInterface $webClient,
        private DhlTokenProviderInterface $dhlTokenProvider,
        private DhlApiClientInterface $dhlApiClient,
        private ShippingLabelStorageInterface $shippingLabelStorage,
        private ObjectManager $shippingExportManager,
        private Registry $registry,
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

        $data = $response->toArray();
        $parcel = current($data['items']);
        $label = $parcel['label'];
        $this->shippingLabelStorage->saveShippingLabel($shippingExport, $label['b64'], $label['fileFormat']);

        $this->markShipmentAsExported($shippingExport, $parcel['shipmentNo']);
    }

    private function markShipmentAsExported(ShippingExportInterface $shippingExport, string $shipmentId): void
    {
        $shippingExport->setState(ShippingExportInterface::STATE_EXPORTED);
        $shippingExport->setExportedAt(new DateTime());
        $shippingExport->setExternalId($shipmentId);
        $shippingExport->getShipment();
        $shipment = $shippingExport->getShipment();
        Assert::notNull($shipment);

        $shipmentWorkflow = $this->registry->get($shipment, ShipmentTransitions::GRAPH);

        if ($shipmentWorkflow->can($shipment, ShipmentTransitions::TRANSITION_SHIP)) {
            $shipment->setTracking($shipmentId);
            $shipment->setShippedAt(new DateTime());
            $shipmentWorkflow->apply($shipment, ShipmentTransitions::TRANSITION_SHIP);
        }

        $this->shippingExportManager->flush();
    }
}
