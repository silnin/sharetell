<?php

use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Silnin\UserBundle\Repository\UserRepository;
use Symfony\Component\HttpKernel\KernelInterface;

class FeatureContext extends MinkContext implements KernelAwareContext
{
    /** @var KernelInterface */
    private $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @When /^I register "([^"]*)"$/
     */
    public function iRegister($email)
    {
        $this->visit('/register');
        $this->fillField('fos_user_registration_form_email', $email);
        $this->fillField('fos_user_registration_form_username', 'bddtest');
        $this->fillField('fos_user_registration_form_plainPassword_first', 'test');
        $this->fillField('fos_user_registration_form_plainPassword_second', 'test');
        $this->pressButton('registration.submit');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     * @Given /^I am logged in$/
     */
    public function iAmLoggedIn()
    {
        $this->visit('/logout');
        $this->visit('/register');
        $this->emailAddressIsAvailable('bddtest@silnin.nl');
        $this->iRegister('bddtest@silnin.nl');
    }

    /**
     * @Given /^there is is a public story named "([^"]*)" available$/
     */
    public function thereIsIsAPublicStoryNamedAvailable($arg1)
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Given /^email address "([^"]*)" is available$/
     */
    public function emailAddressIsAvailable($emailAddress)
    {
        /** @var UserRepository $repo */
        $repo = $this->kernel->getContainer()->get('silnin.user_repository');
        $repo->deleteUserByEmail($emailAddress);
    }
}
