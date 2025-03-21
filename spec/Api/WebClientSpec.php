<?php

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
    function it_is_initializable(): void
    {
        $this->shouldHaveType(WebClient::class);
        $this->shouldHaveType(WebClientInterface::class);
    }

    public function it_create_request_data_shipment(
        ShippingGatewayInterface $shippingGateway,
        ShipmentInterface $shipment,
        OrderInterface $order,
        AddressInterface $address,
        CustomerInterface $customer
    ): void {
        $this->setShippingGateway($shippingGateway);
        $this->setShipment($shipment);

        $shipment->getOrder()->willReturn($order);
        $order->getNumber()->willReturn('123456');

        $shippingGateway->getConfigValue('country_code')->willReturn('US');
        $shippingGateway->getConfigValue('name')->willReturn('John Doe');
        $shippingGateway->getConfigValue('city')->willReturn('New York');
        $shippingGateway->getConfigValue('street')->willReturn('Example Street 12a');
        $shippingGateway->getConfigValue('postal_code')->willReturn('12456');
        $shippingGateway->getConfigValue('email')->willReturn('john@doe.com');
        $shippingGateway->getConfigValue('phone_number')->willReturn('123456789');

        $order->getShippingAddress()->willReturn($address);
        $address->getCountryCode()->willReturn('US');
        $order->getCustomer()->willReturn($customer);
        $address->getFullName()->willReturn('Doe John');
        $address->getCity()->willReturn('Chicago');
        $address->getStreet()->willReturn('Example Street 1a');
        $address->getPostcode()->willReturn('54321');
        $address->getPhoneNumber()->willReturn('987654321');
        $customer->getEmail()->willReturn('doe@john.com');

        $shipment->getShippingWeight()->willReturn(200.0);

        $this->getRefNumber();
        $this->getShipper();
        $this->getConsignee();
        $this->getDetails();
    }
}