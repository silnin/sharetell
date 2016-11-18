Feature: Authorization
  As a admin
  I want users to authenticate before playing
  So that misuse is avoided

  Scenario: register
    Given I am on "logout"
    And email address "bddtest@silnin.nl" is available
    When I register "bddtest@silnin.nl"
    Then I should be on "/register/confirmed"

  Scenario: login
    Given I am registered
    And I am logged out
    When I am on "/login"
    And I fill in the following:
      | _username | bddtest@silnin.nl |
      | _password | test |
    And I press "_submit"
    Then I should be on "/dashboard/"