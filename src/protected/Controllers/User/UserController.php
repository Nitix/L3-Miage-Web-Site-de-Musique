<?php


namespace Controllers\User;


use Controllers\Controller;
use Models\User;

/**
 * General controller for an User, can be for admin and user
 * He can be used to verify if the user is a visitor
 * @package Controllers\User
 */
class UserController
{

    const INVALID_EMAIL = 10;
    const EMAIL_ALREADY_IN_USE = 11;

    /**
     * @var User Actual member
     */
    private $user;

    /**
     * @var bool Define is the user is a visitor
     */
    private $isVisitor;

    /**
     * @param User $user The user
     */
    public function __construct(User $user = null)
    {
        if ($user == null) {
            $this->isVisitor = true;
        } else {
            $this->isVisitor = false;
            $this->user = $user;
        }
    }

    /**
     * Change the password of the user, does not update the database
     * @param String $password Password to set
     * @param bool $checkStrength true if he must check the requirement
     *
     * @throws EmptyPasswordException Exception thrown when the password is empty or null
     * @throws WeakPasswordException Exception thrown when the password is too much weak
     */
    public function updatePassword($password, $checkStrength = true)
    {
        if (!$this->isVisitor()) {
            if ($password == null && empty($password)) {
                throw new EmptyPasswordException();
            }
            if ($checkStrength) {
                if (!UserController::checkPasswordStrength($password)) {
                    throw new WeakPasswordException();
                }
            }
            $password = password_hash($password, PASSWORD_DEFAULT);
            $this->user->setPassword($password);
        }
    }

    /**
     * Check if the password is strong enough
     * Password length must be > 8
     * But it's not really strong
     * @param String $password password to check
     * @return bool true it's strong enough
     */
    public static function checkPasswordStrength($password)
    {
        return strlen($password) > 8;
    }

    /**
     * Return the current user, will return a empty user if it's a visitor
     * @return UserController The current user
     */
    public static function getCurrentUser()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        } else {
            return new UserController();
        }
    }

    /**
     * Verify the user with the password and return the user if the password is correct
     * And then register the user to the session
     *
     * @param String $login Login of the user
     * @param String $password Password of the user
     * @return bool true if the user is logged in
     * @throws EmptyPasswordException
     */
    public static function login($login, $password)
    {
        $user = User::findByLogin($login);
        if (password_verify($password, $user->getPassword())) {
            $userController = new UserController($user);
            if (password_needs_rehash($user->$password, PASSWORD_DEFAULT)) {
                try {
                    $userController->updatePassword($password, false);
                } catch (WeakPasswordException $e) {
                    //Do nothing, this should not occur
                }
            }
            $_SESSION['user'] = $userController;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return if the user is a visitor
     * @return bool true if it's a visitor
     */
    public function isVisitor()
    {
        return $this->isVisitor;
    }

    /**
     * Return the actual user
     * @return User actual user, can be null if it's a visitor
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Check the email, it's me be a correct email and a not user email (returned by class constant)
     * @param String $email email to check
     * @return int result of the check, 0 if it's ok
     */
    public static function checkEmail($email)
    {
        $ok = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($ok) {
            return (User::findByEmail($email) == null) ? self::EMAIL_ALREADY_IN_USE : 0;
        } else {
            return self::INVALID_EMAIL;
        }
    }

    /**
     * Check if the email is used by an another user
     * @param String $email email to check
     * @return bool true it's not used by another member, return false if it's a visitor
     */
    public function checkOtherEmail($email)
    {
        if (!$this->isVisitor()) {
            $user = User::findByEmail($email);
            if ($user == null) {
                return true;
            } else {
                return $this->user->getId() == $user->getId();
            }
        } else {
            return false;
        }
    }

    /**
     * Check if the login is not used
     * @param String $login login to verify
     * @return bool true if it's not used
     */
    public static function checkLogin($login)
    {
        $user = User::findByLogin($login);
        return $user == null;
    }

    /**
     * Register the user to the database
     * Do some verification before inserting
     * @param String $login The login os the user
     * @param String $password the plain password of the user
     * @param String $email the email of the user
     * @return bool true if all it's ok
     */
    public static function register($login, $password, $email)
    {
        $resEmail = self::checkEmail($email);
        if (self::checkLogin($login) &&
            self::checkPasswordStrength($password) &&
            $resEmail != self::INVALID_EMAIL &&
            $resEmail != self::EMAIL_ALREADY_IN_USE
        ) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $user = new User($login, $password, $email);
            $user->insert();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update the current user
     * @param String $password the password to update, if the password it's empty, the password will not be changed
     * @param String $email the email to update
     * @return bool true if all it's ok
     */
    public function update($email, $password = "")
    {
        if (!$this->isVisitor()) {
            if (empty($password)) {
                if (self::checkEmail($email) != self::INVALID_EMAIL &&
                    self::checkOtherEmail($email)
                ) {
                    $this->user->setEmail($email);
                    $this->user->update();
                    return true;
                } else {
                    return false;
                }
            } else {
                if (self::checkPasswordStrength($password) &&
                    self::checkEmail($email) != self::INVALID_EMAIL &&
                    self::checkOtherEmail($email)
                ) {
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $this->user->setPassword($password);
                    $this->user->setEmail($email);
                    $this->user->update();
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
}