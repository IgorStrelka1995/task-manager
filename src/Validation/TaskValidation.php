<?php

namespace App\Validation;

class TaskValidation
{
    const ERROR_CREATED_TASK = 'The task has not been created';
    const ERROR_CREATED_USER = 'The user has not been created';
    const ERROR_NOT_FOUND_TASK = 'The task not found';

    const SUCCESS_CREATED_TASK = 'The task has been created';
    const SUCCESS_UPDATED_TASK = 'The task has been updated';

    /**
     * @access private
     * @var array
     */
    private $errors = [];

    /**
     * @access public
     * @param string $email
     * @return self
     */
    public function validEmail(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Field Email is not valid';
        }

        if (0 === strlen($email)) {
            $this->errors['email'] = 'Field Email should not be blank';
        }

        if (80 < strlen($email)) {
            $this->errors['email'] = 'Field Email should not be longer than 80 characters';
        }

        return $this;
    }

    /**
     * @access public
     * @param string $username
     * @return self
     */
    public function validUsername(string $username): self
    {
        if (0 === strlen($username)) {
            $this->errors['username'] = 'Field Username should not be blank';
        }

        if (50 < strlen($username)) {
            $this->errors['username'] = 'Field Username should not be longer than 50 characters';
        }

        return $this;
    }

    /**
     * @access public
     * @param string $task
     * @return self
     */
    public function validTask(string $task): self
    {
        if (0 === strlen($task)) {
            $this->errors['task'] = 'Field Task should not be blank';
        }

        return $this;
    }

    /**
     * @access public
     * @param $id
     * @return self
     */
    public function validTaskId($id): self
    {
        if (!is_numeric($id)) {
            $this->errors['message'] = 'Task id should be numeric';
        }

        return $this;
    }

    /**
     * @access public
     * @return array
     */
    public function render(): array
    {
        return $this->errors;
    }
}