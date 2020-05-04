<?php

namespace App\Service;

/**
 * @package App\Service
 */
class Session 
{
    /**
     * @access public
     * @return void
     */
    public function sessionStart(): void
    {
        session_start();
    }

    /**
     * @access public
     * @return boolean
     */
    public function isSessionStarted(): bool
    {
        if(session_id() == '') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @access public
     * @param string $session
     * @return boolean
     */
    public function isSessionExists(string $session): bool
    {
        if($this->isSessionStarted() == false) {
            $this->sessionStart();
        }

        if(isset($_SESSION[$session])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @access public
     * @param string $session
     * @param string $value
     * @return bool
     */
    public function set(string $session, string $value): void 
    {
        if($this->isSessionStarted() == false) {
            $this->sessionStart();
        }

        $_SESSION[$session] = $value;
        if($this->isSessionExists($session) == false) {
            throw new \Exception('Unable to Create Session');
        }
    }

    /**
     * @access public
     * @param string $session
     * @return string
     */
    public function get($session): string 
    {
        if(isset($_SESSION[$session])) {
            return $_SESSION[$session];
        } else {
            throw new \Exception('Session Does Not Exist');
        }
    }

    /**
     * @access public
     * @return void
     */
    public function removeSession(): void
    {
        session_destroy();
    }
}