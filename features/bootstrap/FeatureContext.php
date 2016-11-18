<?php

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use Silnin\ShareTell\StoryBundle\Entity\Contribution;
use Silnin\ShareTell\StoryBundle\Entity\Story;
use Silnin\UserBundle\Entity\User;
use Silnin\UserBundle\Repository\UserRepository;
use Symfony\Component\HttpKernel\KernelInterface;

class FeatureContext extends MinkContext implements KernelAwareContext
{
    const EMAIL = 'bddtest@silnin.nl';
    const USERNAME = 'bddtest';
    const PASSWORD = 'test';

    const OTHER_PLAYER_EMAIL = 'anotherbddtest@silnin.nl';
    const OTHER_PLAYER_USERNAME = 'AnotherPlayer';
    const OTHER_PLAYER_PASSWORD = 'test';

    /** @var KernelInterface */
    private $kernel;

    /** @var User */
    private $me;

    /** @var User */
    private $otherPlayer;

    /** @var Story */
    private $myStory;

    /** @var Contribution */
    private $myContribution;

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
        $this->fillField('fos_user_registration_form_username', self::USERNAME);
        $this->fillField('fos_user_registration_form_plainPassword_first', self::PASSWORD);
        $this->fillField('fos_user_registration_form_plainPassword_second', self::PASSWORD);
        $this->pressButton('registration.submit');
        $this->rememberMe($email);
    }

    /**
     * @param string $emailAddress
     * @throws EntityNotFoundException
     */
    private function rememberMe(
        $emailAddress = self::EMAIL
    ) {
        /** @var UserRepository $repo */
        $repo = $this->kernel->getContainer()->get('silnin.user_repository');

        $this->me = $repo->getByEmail($emailAddress);

        if (!($this->me instanceof User) || !$this->me->getId()) {
            throw new EntityNotFoundException('Could not find user ' . self::EMAIL);
        }
    }

    /**
     * @Given /^there is a contribution "([^"]*)"$/
     */
    public function thereIsAContribution($contributionName)
    {
        $repo = $this->kernel->getContainer()->get('silnin.contribution_repository');
        $contribution = $repo->createContribution(
            $this->myStory,
            $this->me,
            $contributionName
        );

        if (!($contribution->getId())) {
            throw new EntityNotFoundException('Contribution was not created');
        }

        $this->myContribution = $contribution;
    }

    /**
     * @Given /^I am registered$/
     */
    public function iAmRegistered()
    {
        $this->iRegister(self::EMAIL);
    }

    /**
     * @Given /^I am logged out$/
     */
    public function iAmLoggedOut()
    {
        $this->visit('/logout');
        $this->assertPageAddress('/');
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
        $this->emailAddressIsAvailable(self::EMAIL);
        $this->iRegister(self::EMAIL);

        $this->rememberMe();
    }

    /**
     * @Given /^there is a public story named "([^"]*)" available$/
     */
    public function thereIsIsAPublicStoryNamedAvailable($storyName)
    {
        $this->createAnotherPlayer();

        $repo = $this->kernel->getContainer()->get('silnin.story_repository');

        $stories = $repo->getPublicActiveStories();
        foreach ($stories as $possibleStory) {
            if ($possibleStory->getTitle() == $storyName) {
                $this->myStory = $possibleStory;
                return;
            }
        }
        $story = $repo->createStory(
            $storyName,
            'public',
            $this->otherPlayer
        );

        if (!$story->getId()) {
            throw new EntityNotFoundException('Story was not created');
        }

        $this->myStory = $story;
    }

    private function createAnotherPlayer()
    {
        $repo = $this->kernel->getContainer()->get('silnin.user_repository');

        $possiblePlayer = $repo->getByEmail(self::OTHER_PLAYER_EMAIL);

        if ($possiblePlayer instanceof User) {
            $this->otherPlayer = $possiblePlayer;
            return;
        }

        $user = $repo->createUser(
            self::OTHER_PLAYER_EMAIL,
            self::OTHER_PLAYER_USERNAME,
            self::OTHER_PLAYER_PASSWORD
        );

        if (!$user->getId()) {
            throw new EntityNotFoundException('User was not found');
        }

        $this->otherPlayer = $user;
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

    /**
     * @AfterScenario @database
     */
    public function cleanDB(AfterScenarioScope $scope)
    {
        $storyRepo = $this->kernel->getContainer()->get('silnin.story_repository');
        $storyRepo->deleteStoriesFromUser($this->me);
        $storyRepo->deleteStoriesFromUser($this->otherPlayer);

        $userRepo = $this->kernel->getContainer()->get('silnin.user_repository');
        $userRepo->deleteUserByEmail(self::EMAIL);
        $userRepo->deleteUserByEmail(self::OTHER_PLAYER_EMAIL);
    }
}
