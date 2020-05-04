<?php

namespace App\Validation;

/**
 * @package App\Validation
 */
class SecurityValidation
{
    /**
     * @access private
     * @var array
     */
    private $errors = [];

    /**
     * @access public
     * @param string $login
     * @return self
     */
    public function validLogin(string $login): self
    {
        if (0 === strlen($login)) {
            $this->errors['login'] = 'Field Login should not be blank';
        }

        return $this;
    }

    /**
     * @access public
     * @param string $password
     * @return self
     */
    public function validPassword(string $password): self
    {
        if (0 === strlen($password)) {
            $this->errors['password'] = 'Field Password should not be blank';
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