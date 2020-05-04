<?php

namespace App\Service;

use App\Model\Admin;

/**
 * @package App\Service
 */
class Auth
{
    const ERROR_INVALID_CREDENTIALS = 'Invalid credentials';

    /**
     * @access private
     * @var obj Admin
     */
    private $admin;

    /**
     * @access private
     * @var obj Session
     */
    private $session;

    public function __construct(Admin $admin, Session $session)
    {
        $this->admin    = $admin;
        $this->session  = $session;
    }

    /**
     * Sign In admin
     *
     * @access public
     * @param string $login
     * @param string $password
     * @return array
     */
    public function login(string $login, string $password): array
    {
        $user = $this->admin->getAdminUser($login);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if ($this->session->isSessionStarted() == false) {
                    $this->session->sessionStart();
                }

                $this->session->set('user_id', $user['id']);
                $result = ['success' => true];
            } else {
                $result = [
                    'success' => false,
                    'message' => Auth::ERROR_INVALID_CREDENTIALS
                ];
            }
        } else {
            $result = [
                'success' => false,
                'message' => Auth::ERROR_INVALID_CREDENTIALS
            ];
        }

        return $result;
    }

    /**
     * Check is admin auth
     *
     * @access public
     * @return boolean
     */
    public function isAuth()
    {
        if ($this->session->isSessionStarted() == false) {
            return false;
        }

        if (!$this->session->isSessionExists('user_id')) {
            return false;
        }

        return true;
    }  

    /**
     * Sign Out admin
     *
     * @access public
     * @return void
     */
    public function logout()
    {
        if ($this->session->isSessionStarted()) {
            $this->session->set('user_id', '');
            $this->session->removeSession();
        }

        return true;
    }
}