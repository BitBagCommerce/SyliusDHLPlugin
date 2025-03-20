<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusDhlPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\BitBag\SyliusDhlPlugin\Behat\Page\Admin\ShippingGateway\CreatePage;
use Webmozart\Assert\Assert;

final class ShippingGatewayContext implements Context
{
    public function __construct(
        private CreatePage $createPage,
        private CurrentPageResolverInterface $currentPageResolver,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }


    /**
     * @When I visit the create shipping gateway configuration page for :code
     */
    public function iVisitTheCreateShippingGatewayConfigurationPage($code): void
    {
        $this->createPage->open(['code' => $code]);
    }

    /**
     * @When I select the :name shipping method
     */
    public function iSelectTheShippingMethod($name): void
    {
        $this->resolveCurrentPage()->selectShippingMethod($name);
    }

    /**
     * @When I fill the :field field with :value
     */
    public function iFillTheFieldWith($field, $value): void
    {
        $this->resolveCurrentPage()->fillField($field, $value);
    }

    /**
     * @When I clear the :field field
     */
    public function iClearTheField($field): void
    {
        $this->resolveCurrentPage()->fillField($field, '');
    }

    /**
     * @When I add it
     * @When I save it
     */
    public function iTryToAddIt(): void
    {
        $this->resolveCurrentPage()->submit();
    }

    /**
     * @Then I should be notified that the shipping gateway has been created
     * @Then I should be notified that the shipping gateway has been updated
     */
    public function iShouldBeNotifiedThatTheShippingGatewayWasCreated(): void
    {
        $this->notificationChecker->checkNotification(
            'Shipping gateway has been successfully',
            NotificationType::success(),
        );
    }

    /**
     * @Then :message error message should be displayed
     */
    public function errorMessageForFieldShouldBeDisplayed($message): void
    {
        Assert::true($this->resolveCurrentPage()->hasError($message));
    }

    /**
     * @When I fill the :field select option with :option
     */
    public function iFillTheSelectOptionWith($filed, $option): void
    {
        $this->resolveCurrentPage()->selectFieldOption($filed, $option);
    }

    private function resolveCurrentPage(): CreatePage
    {
        /** @var CreatePage $page */
        $page = $this->currentPageResolver->getCurrentPageWithForm([
            $this->createPage,
        ]);

        return $page;
    }
}