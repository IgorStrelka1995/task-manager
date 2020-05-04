<?php

namespace App\Controller;

use App\Model\Task;
use App\Model\User;
use App\Validation\TaskValidation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Slim\Flash\Messages;
use Twig\Environment;

/**
 * @package App\Controller
 */
class TaskController
{
    /**
     * @access private
     * @var obj Environment
     */
    private $twig;

    /**
     * @access private
     * @var obj TaskValidation
     */
    private $validation;

    /**
     * @access private
     * @var obj User
     */
    private $user;

    /**
     * @access private
     * @var obj Task
     */
    private $task;

    /**
     * @access private
     * @var obj Messages
     */
    private $flash;

    public function __construct(Environment $twig, TaskValidation $validation, User $user, Task $task, Messages $flash)
    {
        $this->twig         = $twig;
        $this->validation   = $validation;
        $this->user         = $user;
        $this->task         = $task;
        $this->flash        = $flash;
    }

    /**
     * Main page
     *
     * @access public
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function main(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
       $response->getBody()->write(
            $this->twig->render('task/main.twig', [
                'tasks' => $this->task->getTasks()
            ])
        );

        return $response;
    }

    /**
     * Create task page
     *
     * @access public
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function createTask(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(
            $this->twig->render('task/form.twig')
        );

        return $response;
    }

    /**
     * Handler for create task form
     *
     * @access public
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function handlerCreateTask(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

        $email      = htmlspecialchars($body['email']);
        $username   = htmlspecialchars($body['username']);
        $task       = htmlspecialchars($body['task']);

        $errors = $this->validation->validEmail($email)->validUsername($username)->validTask($task)->render();
        if ($errors) {
            $response->getBody()->write(
                $this->twig->render('task/form.twig', [
                    'errors'    => $errors, 
                    'email'     => $email,
                    'username'  => $username,
                    'task'      => $task
                ])
            );
    
            return $response;
        }

        $user_id = $this->user->createUser($username, $email);
        if (!$user_id) {
            $this->flash->addMessage('error', TaskValidation::ERROR_CREATED_USER);
            return new RedirectResponse("/");
        } else {
            $result = $this->task->createTask($task, $user_id);
            if (!$result) {
                $this->flash->addMessage('error', TaskValidation::ERROR_CREATED_TASK);
                return new RedirectResponse("/");
            }
        }

        $this->flash->addMessage('success', TaskValidation::SUCCESS_CREATED_TASK);
        return new RedirectResponse("/");
    }
}