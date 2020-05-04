<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Model\Task;
use App\Service\Auth;
use App\Validation\TaskValidation;
use Laminas\Diactoros\Response\RedirectResponse;
use Twig\Environment;
use League\Route\Http\Exception\BadRequestException;
use Slim\Flash\Messages;

/**
 * @package App\Controller
 */
class AdminController
{
    /**
     * @access private
     * @var obj Environment
     */
    private $twig;

    /**
     * @access private
     * @var obj Task
     */
    private $task;

    /**
     * @access private
     * @var obj TaskValidation
     */
    private $validation;

    /**
     * @access private
     * @var obj Messages
     */
    private $flash;

    /**
     * @access private
     * @var obj Auth
     */
    private $auth;

    public function __construct(Environment $twig, Task $task, TaskValidation $validation, Messages $flash, Auth $auth)
    {
        $this->twig         = $twig;
        $this->task         = $task;
        $this->validation   = $validation;
        $this->flash        = $flash;
        $this->auth         = $auth;
    }

    /**
     * Main admin page
     *
     * @access public
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function adminMainPage(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!$this->auth->isAuth()) {
            return new RedirectResponse("/login");
        }

        $response->getBody()->write(
            $this->twig->render('admin/tasks.twig', [
                'tasks' => $this->task->getTasks()
            ])
        );

        return $response;
    }

    /**
     * Admin single task page
     *
     * @access public
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $arguments
     * @return ResponseInterface
     */
    public function adminSingleTask(ServerRequestInterface $request, ResponseInterface $response, array $arguments): ResponseInterface
    {
        if (!$this->auth->isAuth()) {
            return new RedirectResponse("/login");
        }

        $validId = $this->validation->validTaskId($arguments['id'])->render();
        if ($validId) {
            throw new BadRequestException($validId['message']);
        }

        $task = $this->task->getTask($arguments['id']);
        if (!$task) {
            throw new BadRequestException(TaskValidation::ERROR_NOT_FOUND_TASK);
        }

        $response->getBody()->write(
            $this->twig->render('admin/task.twig', [
                'task' => $task
            ])
        );

        return $response;
    }

    /**
     * Handler for admin edit task form
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $arguments
     * @return ResponseInterface
     */
    public function handlerEditTask(ServerRequestInterface $request, ResponseInterface $response, $arguments): ResponseInterface
    {
        if (!$this->auth->isAuth()) {
            return new RedirectResponse("/login");
        }
        
        $body = $request->getParsedBody();

        $id = $arguments['id'];
        $validId = $this->validation->validTaskId($id)->render();
        if ($validId) {
            throw new BadRequestException($validId['message']);
        }

        $task = htmlspecialchars($body['edit-task']);
        $errors = $this->validation->validTask($task)->render();
        if ($errors) {
            $response->getBody()->write(
                $this->twig->render('admin/task.twig', [
                    'errors'    => $errors,
                    'task'      => $task
                ])
            );

            return $response;
        } 

        $status = isset($body['edit-complete']) ? $body['edit-complete'] : false;
        if ($status) {
            $this->task->changeStatus($id);
        }

        $taskInfo = $this->task->getTask($id);
        if ($taskInfo['text'] !== $task) {
            $this->task->editTask($task, $id);
        }

        $this->flash->addMessage('success', TaskValidation::SUCCESS_UPDATED_TASK);

        return new RedirectResponse("/admin");
    }
}