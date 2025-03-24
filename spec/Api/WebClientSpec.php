<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusDhlPlugin\Api;

use BitBag\SyliusDhlPlugin\Api\WebClient;
use BitBag\SyliusDhlPlugin\Api\WebClientInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class WebClientSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WebClient::class);
        $this->shouldHaveType(WebClientInterface::class);
    }

    public function let(): void
    {
        $this->beConstructedWith('g');
    }

    public function it_create_consignee_data(
        ShipmentInterface $shipment,
        OrderInterface $order,
        AddressInterface $address,
        CustomerInterface $customer,
    ): void {
        $this->setShipment($shipment);

        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $address->getCountryCode()->willReturn('US');
        $order->getCustomer()->willReturn($customer);
        $address->getFullName()->willReturn('Doe John');
        $address->getCity()->willReturn('Chicago');
        $address->getStreet()->willReturn('Example Street 1a');
        $address->getPostcode()->willReturn('54321');
        $address->getPhoneNumber()->willReturn('987654321');
        $customer->getEmail()->willReturn('doe@john.com');

        $this->getConsignee();
    }

    public function it_create_shipper_data(
        ShippingGatewayInterface $shippingGateway,
    ): void {
        $this->setShippingGateway($shippingGateway);

        $shippingGateway->getConfigValue('country_code')->willReturn('US');
        $shippingGateway->getConfigValue('name')->willReturn('John Doe');
        $shippingGateway->getConfigValue('city')->willReturn('New York');
        $shippingGateway->getConfigValue('street')->willReturn('Example Street 12a');
        $shippingGateway->getConfigValue('postal_code')->willReturn('12456');
        $shippingGateway->getConfigValue('email')->willReturn('john@doe.com');
        $shippingGateway->getConfigValue('phone_number')->willReturn('123456789');

        $this->getShipper();
    }

    public function it_create_details_data(
        ShipmentInterface $shipment,
        OrderInterface $order,
    ): void {
        $this->setShipment($shipment);

        $shipment->getShippingWeight()->willReturn(200.0);
        $shipment->getOrder()->willReturn($order);

        $this->getDetails()->shouldBeLike([
            'weight' => [
                'uom' => 'g',
                'value' => 200,
            ],
        ]);
    }

    public function it_create_ref_number_data(
        ShipmentInterface $shipment,
        OrderInterface $order,
    ): void {
        $this->setShipment($shipment);

        $shipment->getOrder()->willReturn($order);
        $order->getNumber()->willReturn('123456');

        $this->getRefNumber()->shouldBeLike('Order 123456');
    }
}
