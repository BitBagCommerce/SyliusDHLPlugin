<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusDhlPlugin\Resolver;

use BitBag\SyliusDhlPlugin\Enum\DhlEnvironmentEnum;
use BitBag\SyliusDhlPlugin\Resolver\DhlApiUrlResolver;
use BitBag\SyliusDhlPlugin\Resolver\DhlApiUrlResolverInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use PhpSpec\ObjectBehavior;

final class DhlApiUrlResolverSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DhlApiUrlResolver::class);
        $this->shouldHaveType(DhlApiUrlResolverInterface::class);
    }

    public function let(): void
    {
        $this->beConstructedWith(
            'https://api.prod.com',
            'https://api.sandbox.com',
        );
    }

    public function it_resolve_url_for_production_environment(
        ShippingGatewayInterface $shippingGateway,
    ): void {
        $shippingGateway->setConfig(['environment' => DhlEnvironmentEnum::PRODUCTION->value]);

        $shippingGateway->getConfigValue('environment')->willReturn(DhlEnvironmentEnum::PRODUCTION->value);

        $this->resolve($shippingGateway)->shouldBeLike('https://api.prod.com');
    }

    public function it_resolve_url_for_sandbox_environment(
        ShippingGatewayInterface $shippingGateway,
    ): void {
        $shippingGateway->setConfig(['environment' => DhlEnvironmentEnum::SANDBOX->value]);

        $shippingGateway->getConfigValue('environment')->willReturn(DhlEnvironmentEnum::SANDBOX->value);

        $this->resolve($shippingGateway)->shouldBeLike('https://api.sandbox.com');
    }

    public function it_resolve_url_for_unknown_environment(
        ShippingGatewayInterface $shippingGateway,
    ): void {
        $shippingGateway->setConfig(['environment' => 'unknown']);

        $shippingGateway->getConfigValue('environment')->willReturn('unknown');

        $this->resolve($shippingGateway)->shouldBeLike('https://api.sandbox.com');
    }
}
