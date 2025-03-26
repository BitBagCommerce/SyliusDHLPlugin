<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusDhlPlugin\EventListener;

use BitBag\SyliusDhlPlugin\Exporter\DhlShipmentExporter;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Webmozart\Assert\Assert;

final class DhlShippingExportEventListener
{
    public function __construct(
        private RequestStack $requestStack,
        private DhlShipmentExporter $dhlShipmentExporter,
        private LoggerInterface $shippingExportLogger,
    ) {
    }

    public function exportShipment(ResourceControllerEvent $exportShipmentEvent): void
    {
        $shippingExport = $exportShipmentEvent->getSubject();
        Assert::isInstanceOf($shippingExport, ShippingExportInterface::class);

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        try {
            $this->dhlShipmentExporter->export($shippingExport);
        } catch(Exception $exception) {
            $order = $shippingExport->getShipment()?->getOrder();
            $this->logError($exception);

            $session->getFlashBag()->add('error', sprintf(
                'DHL Web Service for #%s order: %s',
                $order?->getNumber(),
                $exception->getMessage(),
            ));

            return;
        }

        $session->getFlashBag()->add('success', 'bitbag.ui.shipment_data_has_been_exported');
    }

    private function logError(Exception $exception): void
    {
        $context = [
            'trace' => $exception->getTrace(),
        ];
        if ($exception instanceof ClientException) {
            $context['response'] = $exception->getResponse()->getContent(false);
        }

        $this->shippingExportLogger->error($exception->getMessage(), $context);
    }
}
