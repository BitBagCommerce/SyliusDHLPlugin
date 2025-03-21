@managing_shipping_gateway_dhl
Feature: Creating shipping gateway
  In order to export shipping data to external shipping provider service
  As an Administrator
  I want to be able to add new shipping gateway with shipping method

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator
    And the store has "DHL Express" shipping method with "$6.44" fee

  @ui
  Scenario: Creating DHL Express shipping gateway
    When I visit the create shipping gateway configuration page for "dhl"
    And I select the "DHL Express" shipping method
    And I fill the "Username" field with "john_doe"
    And I fill the "Password" field with "secret_password"
    And I fill the "Client id" field with "secret"
    And I fill the "Client secret" field with "super_secret"
    And I fill the "Environment" select option with "sandbox"
    And I fill the shipper address information
    And I add it
    Then I should be notified that the shipping gateway has been created