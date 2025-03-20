<?php

namespace BitBag\Acme\SyliusDhlPlugin\Unit\Resolver;

use BitBag\SyliusDhlPlugin\Enum\DhlEnvironmentEnum;
use PHPUnit\Framework\TestCase;
use Tests\BitBag\SyliusDhlPlugin\Unit\MotherObject\DhlApiUrlResolverMother;
use Tests\BitBag\SyliusDhlPlugin\Unit\MotherObject\ShippingGatewayMother;

class DhlApiUrlResolverTest extends TestCase
{
    public function testResolveForProductionEnvironment(): void
    {
        $dhApiUrlResolver = DhlApiUrlResolverMother::create();
        $shippingGateway = ShippingGatewayMother::createWithEnvironment(DhlEnvironmentEnum::PRODUCTION->value);

        $result = $dhApiUrlResolver->resolve($shippingGateway);

        self::assertSame('production_url', $result);
    }

    public function testResolveForSandboxEnvironment(): void
    {
        $dhApiUrlResolver = DhlApiUrlResolverMother::create();
        $shippingGateway = ShippingGatewayMother::createWithEnvironment(DhlEnvironmentEnum::SANDBOX->value);

        $result = $dhApiUrlResolver->resolve($shippingGateway);

        self::assertSame('sandbox_url', $result);
    }

    public function testResolveForUnknownEnvironment(): void
    {
        $dhApiUrlResolver = DhlApiUrlResolverMother::create();
        $shippingGateway = ShippingGatewayMother::createWithEnvironment('unknown_environment');

        $result = $dhApiUrlResolver->resolve($shippingGateway);

        self::assertSame('sandbox_url', $result);
    }
}