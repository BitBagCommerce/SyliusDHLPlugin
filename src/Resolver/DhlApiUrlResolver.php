<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\Resolver;

use BitBag\SyliusDhlPlugin\Enum\DhlEnvironmentEnum;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Webmozart\Assert\Assert;

final class DhlApiUrlResolver implements DhlApiUrlResolverInterface
{
    public function __construct(
        private string $productionApiUrl,
        private string $sandboxApiUrl,
    ) {
    }

    public function resolve(ShippingGatewayInterface $shippingGateway): string
    {
        return match ($shippingGateway->getConfigValue('environment')) {
            DhlEnvironmentEnum::PRODUCTION->value => $this->productionApiUrl,
            default => $this->sandboxApiUrl,
        };
    }
}
