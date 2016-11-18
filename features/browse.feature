Feature: Story browser
  As a customer
  I want to see which stories are available for joining
  So I can join them

  Scenario: public games overview
    Given I am logged in
    And there is a public story named "Kumbaya" available
    When I am on "/dashboard"
    Then I should see text matching "Kumbaya"

  Scenario: join public game
    Given I am logged in
    And there is a public story named "Kumbaya" available
    And there is a contribution "Once upon a time"
    And I am on "/dashboard"
    When I follow "Kumbaya"
    Then I should see "Once upon a time"