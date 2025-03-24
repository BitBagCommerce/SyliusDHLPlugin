<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\TwigExtension;

use BitBag\SyliusShippingExportPlugin\Repository\ShippingExportRepositoryInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DhlShippingExportExtension extends AbstractExtension
{
    public function __construct(
        private ShippingExportRepositoryInterface $shippingExportRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_shipping_export_id', [$this, 'getShippingExportId']),
        ];
    }

    public function getShippingExportId(ShipmentInterface $shipment): ?string
    {
        $shippingExport = $this->shippingExportRepository->findOneBy(['shipment' => $shipment]);

        return $shippingExport?->getId();
    }
}
