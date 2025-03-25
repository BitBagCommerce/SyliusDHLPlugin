<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\Storage;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

final class ShippingLabelStorage implements ShippingLabelStorageInterface
{
    public function __construct(
        private Filesystem $fileSystem,
        private string $shippingLabelsPath,
    ) {
    }

    public function saveShippingLabel(
        ShippingExportInterface $shippingExport,
        string $labelContent,
        string $labelExtension,
    ): void {
        $labelPath = $this->shippingLabelsPath
            . \DIRECTORY_SEPARATOR . $this->getFilename($shippingExport)
            . '.' . strtolower($labelExtension);

        $labelPdf = base64_decode($labelContent, true);
        Assert::notFalse($labelPdf);

        $this->fileSystem->dumpFile($labelPath, $labelPdf);
        $shippingExport->setLabelPath($labelPath);
    }

    private function getFilename(ShippingExportInterface $shippingExport): string
    {
        $shipment = $shippingExport->getShipment();
        Assert::notNull($shipment);

        $order = $shipment->getOrder();
        Assert::notNull($order);

        $orderNumber = $order->getNumber();

        $shipmentId = $shipment->getId();

        return implode(
            '_',
            [
                $shipmentId,
                preg_replace('~[^A-Za-z0-9]~', '', (string) $orderNumber),
            ],
        );
    }
}
