<?php

namespace Tests\BitBag\SyliusDhlPlugin\Unit\MotherObject;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGateway;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;

final class ShippingGatewayMother
{
    public static function createWithEnvironment(string $environment): ShippingGatewayInterface
    {
        $shippingGateway = new ShippingGateway();

        $shippingGateway->setConfig(['environment' => $environment]);

        return $shippingGateway;
    }
}