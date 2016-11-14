Feature: Story browser
  As a customer
  I want to see which stories are available for joining
  So I can join them

  Scenario: public games overview
    Given I am logged in
    And there are public stories available
    When I log in
    Then I see available public stories
