<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\Form\Type;

use BitBag\SyliusDhlPlugin\Enum\DhlEnvironmentEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ShippingGatewayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'bitbag_sylius_dhl_plugin.ui.username',
            ])
            ->add('password', TextType::class, [
                'label' => 'bitbag_sylius_dhl_plugin.ui.password',
            ])
            ->add('client_id', TextType::class, [
                'label' => 'bitbag_sylius_dhl_plugin.ui.client_id',
            ])
            ->add('client_secret', TextType::class, [
                'label' => 'bitbag_sylius_dhl_plugin.ui.client_secret',
            ])
            ->add('environment', ChoiceType::class, [
                'label' => 'bitbag_sylius_dhl_plugin.ui.environment',
                'choices' => [
                    'bitbag_sylius_dhl_plugin.ui.environments.production' => DhlEnvironmentEnum::PRODUCTION->value,
                    'bitbag_sylius_dhl_plugin.ui.environments.sandbox' => DhlEnvironmentEnum::SANDBOX->value,
                ],
            ])
            ->add('address', DhlShipperAddressType::class, [
                'label' => false,
            ])
        ;
    }
}
