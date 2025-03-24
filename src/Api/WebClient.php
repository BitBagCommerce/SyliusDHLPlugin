<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\Api;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Intl\Countries;
use Webmozart\Assert\Assert;

final class WebClient implements WebClientInterface
{
    private ShippingGatewayInterface $shippingGateway;

    private ShipmentInterface $shipment;

    public function __construct(
        private string $weightUom,
    ) {
    }

    public function setShippingGateway(ShippingGatewayInterface $shippingGateway): void
    {
        $this->shippingGateway = $shippingGateway;
    }

    public function setShipment(ShipmentInterface $shipment): void
    {
        $this->shipment = $shipment;
    }

    public function getShipper(): array
    {
        $countryCode = $this->getShippingGatewayConfig('country_code');
        Assert::string($countryCode);

        return [
            'name1' => $this->getShippingGatewayConfig('name'),
            'city' => $this->getShippingGatewayConfig('city'),
            'addressStreet' => $this->getShippingGatewayConfig('street'),
            'postalCode' => $this->getShippingGatewayConfig('postal_code'),
            'country' => Countries::getAlpha3Code($countryCode),
            'email' => $this->getShippingGatewayConfig('email'),
            'phone' => $this->getShippingGatewayConfig('phone_number'),
        ];
    }

    public function getConsignee(): array
    {
        $order = $this->shipment->getOrder();
        Assert::notNull($order);

        $shippingAddress = $order->getShippingAddress();
        Assert::notNull($shippingAddress);

        $countryCode = $shippingAddress->getCountryCode();
        Assert::notNull($countryCode);

        $customer = $order->getCustomer();
        Assert::notNull($customer);

        return [
            'name1' => $shippingAddress->getFullName(),
            'city' => $shippingAddress->getCity(),
            'addressStreet' => $shippingAddress->getStreet(),
            'postalCode' => $shippingAddress->getPostcode(),
            'country' => Countries::getAlpha3Code($countryCode),
            'phone' => $shippingAddress->getPhoneNumber(),
            'email' => $customer->getEmail(),
        ];
    }

    public function getDetails(): array
    {
        $weight = $this->shipment->getShippingWeight();

        if (method_exists($this->getOrder(), 'getCustomWeight') && $this->getOrder()->getCustomWeight()) {
            $weight = $this->getOrder()->getCustomWeight();
        }

        return [
            'weight' => [
                'uom' => $this->weightUom,
                'value' => (int) $weight,
            ],
        ];
    }

    public function getRefNumber(): string
    {
        $order = $this->shipment->getOrder();
        Assert::notNull($order);

        return sprintf('Order %s', $order->getNumber());
    }

    private function getShippingGatewayConfig(string $config): mixed
    {
        return $this->shippingGateway->getConfigValue($config);
    }

    private function getOrder(): OrderInterface
    {
        $order = $this->shipment->getOrder();
        Assert::notNull($order);

        return $order;
    }
}
