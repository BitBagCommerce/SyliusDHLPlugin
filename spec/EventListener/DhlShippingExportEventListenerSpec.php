<?php

namespace spec\BitBag\SyliusDhlPlugin\EventListener;

use BitBag\SyliusDhlPlugin\Api\WebClientInterface;
use BitBag\SyliusDhlPlugin\Provider\DhlTokenProviderInterface;
use BitBag\SyliusDhlPlugin\Resolver\DhlApiUrlResolverInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use PhpSpec\ObjectBehavior;
use BitBag\SyliusDhlPlugin\EventListener\DhlShippingExportEventListener;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class DhlShippingExportEventListenerSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(DhlShippingExportEventListener::class);
    }

    public function let(
        WebClientInterface $webClient,
        DhlTokenProviderInterface $dhlTokenProvider,
        RequestStack $requestStack,
        HttpClientInterface $httpClient,
        DhlApiUrlResolverInterface $apiUrlResolver,
    ): void {
        $this->beConstructedWith(
            $webClient,
            $dhlTokenProvider,
            $requestStack,
            $httpClient,
            $apiUrlResolver
        );
    }

    public function it_export_shipment(
        ResourceControllerEvent $exportShipmentEvent,
        WebClientInterface $webClient,
        ShippingExportInterface $shippingExport,
        ShipmentInterface $shipment,
        ShippingGatewayInterface $shippingGateway,
        RequestStack $requestStack,
        Session $session,
        DhlTokenProviderInterface $dhlTokenProvider,
        HttpClientInterface $httpClient,
        DhlApiUrlResolverInterface $apiUrlResolver,
        ResponseInterface $response,
    ): void {
        $exportShipmentEvent->getSubject()->willReturn($shippingExport);

        $shippingExport->getShippingGateway()->willReturn($shippingGateway);

        $shippingGateway->getCode()->willReturn(DhlShippingExportEventListener::DHL_GATEWAY_CODE);

        $shippingExport->getShipment()->willReturn($shipment);

        $webClient->setShippingGateway($shippingGateway)->shouldBeCalled();
        $webClient->setShipment($shipment)->shouldBeCalled();

        $requestStack->getSession()->willReturn($session);

        $dhlTokenProvider->getAccessToken($shippingGateway)->willReturn('example-access-token');
        $apiUrlResolver->resolve($shippingGateway)->willReturn('https://sandbox.url.com');

        $webClient->getRefNumber()->willReturn('Order 203');
        $webClient->getShipper()->willReturn([

        ]);
        $webClient->getConsignee()->willReturn([]);
        $webClient->getDetails()->willReturn([]);

        $httpClient->request('POST', Argument::any(), Argument::any())->willReturn($response);

        $session->getFlashBag()->shouldBeCalled();

        $this->exportShipment($exportShipmentEvent);
    }
}