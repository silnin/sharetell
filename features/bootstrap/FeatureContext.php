<?php

use Behat\MinkExtension\Context\MinkContext;

class FeatureContext extends MinkContext
{
    /**
     * @Given /^I am logged out$/
     */
    public function iAmLoggedOut()
    {
//        $this->mink->resetSessions()
//        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Given /^username "([^"]*)" is available$/
     */
    public function usernameIsAvailable($arg1)
    {
//        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Given /^(?:|I )am logged in$/
     */
    public function iAmLoggedIn()
    {
        $this->visit('/');
        $this->fillField('email', 'gft_bak@hotmail.com');
        $this->fillField('password', 'blabla');
        $this->pressButton('login');
    }

    /**
     * @Given /^(?:|I )am registered$/
     */
    public function iAmRegistered()
    {
        $this->visit('/user/register');
        $this->fillField('email', 'gft_bak@hotmail.com');
        $this->fillField('password', 'blabla');
        $this->fillField('password2', 'blabla');
        $this->pressButton('Register!');
    }


}
