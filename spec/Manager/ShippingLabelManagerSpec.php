<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusDhlPlugin\Manager;

use BitBag\SyliusDhlPlugin\Manager\ShippingLabelManager;
use BitBag\SyliusDhlPlugin\Manager\ShippingLabelManagerInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Filesystem\Filesystem;

final class ShippingLabelManagerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingLabelManager::class);
        $this->shouldImplement(ShippingLabelManagerInterface::class);
    }

    public function let(
        Filesystem $filesystem,
        ObjectManager $shippingExportManager,
    ): void {
        $this->beConstructedWith(
            $filesystem,
            $shippingExportManager,
            '/shipping_labels',
        );
    }

    public function it_should_save_shipping_labels(
        ShippingExportInterface $shippingExport,
        Filesystem $filesystem,
        ShipmentInterface $shipment,
        OrderInterface $order,
    ): void {
        $base64Content = 'YQ==';
        $decodedContent = 'a';

        $shippingExport->getShipment()->willReturn($shipment);
        $shipment->getOrder()->willReturn($order);
        $order->getNumber()->willReturn('333');
        $shipment->getId()->willReturn('4');
        $filesystem->dumpFile('/shipping_labels/4_333.pdf', $decodedContent)->shouldBeCalled();
        $shippingExport->setLabelPath('/shipping_labels/4_333.pdf')->shouldBeCalled();

        $this->saveShippingLabel($shippingExport, $base64Content, 'pdf');
    }
}
