<?php

namespace Silnin\OAuthBundle\Controller;

//use Symfony\Component\Form\FormView;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\OAuthServerBundle\Controller\AuthorizeController as BaseAuthorizeController;
use Silnin\OAuthBundle\Form\Model\Authorize;
use Silnin\OAuthBundle\Entity\Client;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthorizeController extends BaseAuthorizeController
{
    public function authorizeAction(Request $request)
    {
        if (!$request->get('client_id')) {
            throw new NotFoundHttpException("Client id parameter {$request->get('client_id')} is missing.");
        }

        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->findClientByPublicId($request->get('client_id'));

        if (!($client instanceof Client)) {
            throw new NotFoundHttpException("Client {$request->get('client_id')} is not found.");
        }

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $form = $this->container->get('silnin.oauth.authorize.form');
        $formHandler = $this->container->get('silnin.oauth.authorize.form_handler');

        $authorize = new Authorize();

        if (($response = $formHandler->process($authorize)) !== false) {
            return $response;
        }

//        return new JsonResponse(['client' => $request->get('client_id')]);
        $params = [];
        $params['action'] = $this->container->get('router')->generate(
            'fos_oauth_server_authorize',
            [
                'client_id' => $request->get('client_id'),
                'response_type' => $request->get('response_type'),
                'redirect_uri' => $request->get('redirect_uri'),
                'state' => $request->get('state'),
                'scope' => $request->get('scope'),
            ]
        );
        $params['form'] = $form->createView();
        $params['client'] = $client;

        return $this->container->get('templating')->renderResponse(
            'SilninOAuthBundle:Authorize:authorize.html.twig',
            $params
        );
    }
}
