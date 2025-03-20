<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusDhlPlugin\Behat\Page\Admin\ShippingGateway;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $name
     */
    public function selectShippingMethod($name): void;

    /**
     * @param string $field
     * @param string $option
     */
    public function selectFieldOption($field, $option): void;

    /**
     * @param string $field
     * @param string $value
     */
    public function fillField($field, $value): void;

    public function submit(): void;
}