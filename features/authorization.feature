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
    Given I am logged in
    And I am on "/logout"
    When I am on "/login"
    And I fill in the following:
      | _username | bddtest@silnin.nl |
      | _password | test              |
    Then I am on "/dashboard"