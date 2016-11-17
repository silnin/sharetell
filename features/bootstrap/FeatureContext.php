<?php

use Behat\MinkExtension\Context\MinkContext;

class FeatureContext extends MinkContext
{

    /**
     * @Given /^I am logged in$/
     */
    public function iAmLoggedIn()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Given /^there is is a public story named "([^"]*)" available$/
     */
    public function thereIsIsAPublicStoryNamedAvailable($arg1)
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }
}
