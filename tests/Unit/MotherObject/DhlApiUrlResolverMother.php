<?php

namespace Tests\BitBag\SyliusDhlPlugin\Unit\MotherObject;

use BitBag\SyliusDhlPlugin\Resolver\DhlApiUrlResolver;
use BitBag\SyliusDhlPlugin\Resolver\DhlApiUrlResolverInterface;

final class DhlApiUrlResolverMother
{
    public static function create(): DhlApiUrlResolverInterface
    {
        return new DhlApiUrlResolver('production_url', 'sandbox_url');
    }
}