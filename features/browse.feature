Feature: Story browser
  As a customer
  I want to see which stories are available for joining
  So I can join them

  Scenario: public games overview
    Given I am logged in
    And there is is a public story named "Kumbaya" available
    When I am on "/dashboard"
    Then I should see text matching "Kumbaya"
