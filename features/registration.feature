Feature: Registration
  As a customer
  I want to register myself
  So I can play the game

  Scenario: Registration button on frontpage
    Given I am on "/"
    When I press "register"
    Then I should see "registration"

  Scenario: Successful registration
    Given I am logged out
    And username "gft_bak@hotmail.com" is available
    And I am on "/user/register"
    When I fill in the following:
      | email | gft_bak@hotmail.com |
      | password | blabla |
      | password2 | blabla |
    And I press "Register!"
    Then I should see "Thank you."

  Scenario: Login
    Given I am registered
    And I am logged out
    And I am on "/"
    When I fill in the following:
    | email | gft_bak@hotmail.com |
    | password | blabla |
    And I press "login"
    Then I should see "welcome"

  Scenario: Logout
    Given I am registered
    And I am logged in
    And I am on "/browse"
    When I follow "log out"
    Then I should see "You don't just share what you tell, you share the telling itself!"
