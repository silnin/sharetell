<?php

namespace Silnin\OAuthBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Silnin\OAuthBundle\Form\Model\Authorize;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
//use Symfony\Component\Security\Core\SecurityContextInterface;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use OAuth2\OAuth2RedirectException;

class AuthorizeFormHandler
{
    /** @var Form */
    protected $form;

    /** @var TokenStorageInterface */
    protected $context;

    /** @var OAuth2 */
    protected $oauth2;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(Form $form, RequestStack $requestStack, TokenStorageInterface $context, OAuth2 $oauth2)
    {
        $this->form = $form;
        $this->context = $context;
        $this->oauth2 = $oauth2;
        $this->requestStack = $requestStack;
    }

    public function process(Authorize $authorize)
    {
        $this->form->setData($authorize);

        $request = $this->requestStack->getCurrentRequest();

        if ($request->getMethod() == 'POST') {

            $this->form->handleRequest($request);

            if ($this->form->isValid()) {

                try {
                    $user = $this->context->getToken()->getUser();
                    return $this->oauth2->finishClientAuthorization(true, $user, $request, null);
                } catch (OAuth2ServerException $e) {
                    return $e->getHttpResponse();
                }

            }

        }

        return false;
    }

}