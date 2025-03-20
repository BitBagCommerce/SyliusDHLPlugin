<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\EventListener;

use BitBag\SyliusDhlPlugin\Api\WebClientInterface;
use BitBag\SyliusDhlPlugin\Provider\DhlTokenProviderInterface;
use BitBag\SyliusDhlPlugin\Resolver\DhlApiUrlResolverInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use Exception;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

final class DhlShippingExportEventListener
{
    public function __construct(
        private WebClientInterface $webClient,
        private DhlTokenProviderInterface $dhlTokenProvider,
        private RequestStack $requestStack,
        private HttpClientInterface $httpClient,
        private DhlApiUrlResolverInterface $apiUrlResolver,
    ) {
    }

    public function exportShipment(ResourceControllerEvent $exportShipmentEvent): void
    {
        $shippingExport = $exportShipmentEvent->getSubject();
        Assert::isInstanceOf($shippingExport, ShippingExportInterface::class);

        $shippingGateway = $shippingExport->getShippingGateway();
        Assert::notNull($shippingGateway);

        if ('dhl' !== $shippingGateway->getCode()) {
            return;
        }

        $shipment = $shippingExport->getShipment();
        Assert::notNull($shipment);

        $this->webClient->setShippingGateway($shippingGateway);
        $this->webClient->setShipment($shipment);

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        try {
            $accessToken = $this->dhlTokenProvider->getAccessToken($shippingGateway);
            $response = $this->httpClient->request(
                'POST',
                $this->apiUrlResolver->resolve($shippingGateway) . '/parcel/de/shipping/v2/orders',
                [
                    'headers' => [
                        'accept' => 'application/json',
                        'content-type' => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                    'json' => [
                        'shipments' => [
                            [
                                'product' => 'V01PAK',
                                'billingNumber' => '33333333330101',
                                'refNo' => $this->webClient->getRefNumber(),
                                'shipper' => $this->webClient->getShipper(),
                                'consignee' => $this->webClient->getConsignee(),
                                'details' => $this->webClient->getDetails(),
                            ],
                        ],
                    ],
                ],
            );
        } catch(Exception $exception) {
            $session->getFlashBag()->add('error', sprintf(
                'DHL Web Service for #%s order: %s',
                null !== $shipment->getOrder() ? (string) $shipment->getOrder()->getNumber() : '',
                $exception->getMessage(),
            ));

            return;
        }

        $session->getFlashBag()->add('success', 'bitbag_sylius_dhl_plugin.ui.shipment_data_has_been_exported');
    }
}
