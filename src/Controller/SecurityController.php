<?php

namespace App\Controller;

use App\Service\Auth;
use App\Validation\SecurityValidation;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

/**
 * @package App\Controller
 */
class SecurityController
{
    /**
     * @access private
     * @var obj Environment
     */
    private $twig;
    
    /**
     * @access private
     * @var obj SecurityValidation
     */
    private $validation;

    /**
     * @access private
     * @var obj Auth
     */
    private $auth;

    public function __construct(Environment $twig, SecurityValidation $validation, Auth $auth)
    {
        $this->twig         = $twig;
        $this->validation   = $validation;
        $this->auth         = $auth;
    }

    /**
     * Sign In page
     *
     * @access public
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function signInPage(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(
            $this->twig->render('security/form.twig')
        );

        return $response;
    }

    /**
     * Handler for sign in form
     *
     * @access public
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function handlerSignInForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

        $login      = $body['login'];
        $password   = $body['password'];

        $errors = $this->validation->validLogin($login)->validPassword($password)->render();
        if ($errors) {
            $response->getBody()->write(
                $this->twig->render('security/form.twig', [
                    'errors' => $errors, 
                    'login' => $login,
                    'password' => $password,
                ])
            );

            return $response;
        }

        $auth = $this->auth->login($login, $password);
        if (!$auth['success']) {
            $response->getBody()->write(
                $this->twig->render('security/form.twig', [
                    'message' => $auth['message'], 
                    'login' => $login
                ])
            );

            return $response;
        }

        return new RedirectResponse("/admin");
    }

    /**
     * Sign Out admin
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function adminSignOut(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->auth->logout();
        return new RedirectResponse("/");
    }
}