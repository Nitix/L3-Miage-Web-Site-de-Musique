<?php


namespace Models;

use PDO;

/**
 * Represent an user in database
 * Give native method to respect database integrity and user security
 * @package Models
 */
class User
{
    /**
     * Define the max length of an username
     */
    const MAX_USERNAME_STR_LENGTH = 30;

    /**
     * Define the max length of a string that is gonna to be inserted to the database
     */
    const MAX_STR_LENGTH = 255;

    /**
     * @var int Id of the user
     */
    private $id;

    /**
     * @var string Username of the user
     */
    private $username;

    /**
     * @var string Password of the user
     */
    private $password;

    /**
     * @var string Email of the user
     */
    private $email;

    /**
     * @var bool True if the user is a visitor (not connected)
     */
    private $isVisitor = true;

    /**
     * Return the email of the user
     * @return string Email of the user
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the new email of the user
     * @param string $email email of user
     * @throws \InvalidArgumentException When the email is incorrect
     *  Email must be :
     *      - valid
     *      - not in use
     *      - inferior to 256
     */
    public function setEmail($email)
    {
        if (!self::isValidEmail($email) || self::isEmailAlreadyInUse($email)) {
            throw new \InvalidArgumentException();
        }
        $this->email = $email;
    }

    /**
     * Indicate if the user is a visitor
     * @return boolean true if it's a visitor
     */
    public function isVisitor()
    {
        return $this->isVisitor;
    }

    /**
     * Define is the user is a visitor
     * @param boolean $isVisitor true if it's a visitor
     * @throws \InvalidArgumentException A registered user must have
     *      - an email
     *      - an username
     *      - a password
     */
    public function setIsVisitor($isVisitor)
    {
        if ($isVisitor) {
            if (self::isVisitor()) {
                throw new \InvalidArgumentException();
            }
        }
        $this->isVisitor = $isVisitor;
    }

    /**
     * Return the password hashed of the user
     * @return string Password of the user
     */
    private function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the new password, the password will be hashed
     * @param string $password password in plain text
     */
    public function setPassword($password)
    {
        if (!isset($password) || self::isWeakPassword($password)) {
            throw new \InvalidArgumentException();
        }
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Return the user id
     * @return int the user id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the user id
     * @param int $id
     */
    private function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Return thr username of the user
     * @return string username of the user
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the username of the user
     * @param string $username username of the user
     */
    public function setUsername($username)
    {
        if (!self::isValidUsername($username) || self::isUsernameAlreadyInUse($username)) {
            throw new \InvalidArgumentException();
        }
        $this->username = htmlspecialchars($username);
    }

    /**
     * Indicate if the email is valid
     * @param string $email the email to verify
     * @return bool true if it's correct
     */
    public static function isValidEmail($email)
    {
        return strlen($email) <= self::MAX_STR_LENGTH && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Indicate if the email il already in use
     * @param string $email email to verify
     * @return bool true if in use
     */
    public static function isEmailAlreadyInUse($email)
    {
        $db = Base::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) AS count_emails FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch();
        $stmt->closeCursor();

        return $result['count_emails'] > 0;
    }

    /**
     * Check if the password is strong enough
     * Password length must be > 8
     * But it's not really strong
     * @param string $password password to check
     * @return bool true it's strong enough
     */
    public static function isWeakPassword($password)
    {
        return strlen($password) > 8;
    }

    /**
     * Verify if the username is valid
     * @param string $username name of the user
     * @return bool true if it's correct
     */
    public static function isValidUsername($username)
    {
        return isset($username) && strlen($username) > 1 && strlen($username) <= self::MAX_USERNAME_STR_LENGTH;
    }

    /**
     * Check if a username is already taken
     * @param string $username the username to verify
     * @return bool true if in use
     * @throws \PDOException
     */
    public static function isUsernameAlreadyInUse($username)
    {
        $db = Base::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) AS count_username FROM users WHERE username = :username");
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result['count_username'] > 0;
    }


    /**
     * Retrieves all Users in the database.
     * @return User[] array of users
     * @throws \PDOException
     */
    public static function findAll()
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM users;");
        $stmt->execute();


        $tab = array();
        foreach ($stmt->fetchALL() as $us) {
            $u = new User();
            $u->id = $us['id'];
            $u->username = $us['username'];
            $u->password = $us['password'];
            $u->email = $us['email'];
            $u->setIsVisitor(false);

            $tab[$us['id']] = $u;
        }
        $stmt->closeCursor();
        return $tab;
    }

    /**
     * Retrieve an User through his id.
     * @param int $id Id of the user
     * @return User|false The user or null if not found
     * @throws \PDOException
     */
    public static function findByID($id)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM users WHERE user_id=:id ;");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $response = $stmt->fetch();

        if ($response) {
            $u = new User();
            $u->id = $response['user_id'];
            $u->username = $response['username'];
            $u->password = $response['password'];
            $u->email = $response['email'];
            $u->setIsVisitor(false);

            $stmt->closeCursor();
            return $u;
        } else {
            return false;
        }
    }

    /**
     * Retrieve an User through his username
     * @param string $username username of the user
     * @return User|false The user or null if not found
     * @throws \PDOException
     */
    public static function findByUsername($username)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM users WHERE username=:username ;");
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        $response = $stmt->fetch();

        if ($response) {
            $u = new User();
            $u->id = $response['user_id'];
            $u->username = $response['username'];
            $u->password = $response['password'];
            $u->email = $response['email'];
            $u->setIsVisitor(false);
            $stmt->closeCursor();
            return $u;
        } else {
            return false;
        }
    }

    /**
     * Return the current user, will return a empty user if it's a visitor
     * @return User The current user
     */
    public static function getCurrentUser()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        } else {
            return new User();
        }
    }

    /**
     * Verify the user with the password and return the user if the password is correct
     * And then register the user to the session
     *
     * @param String $username Login of the user
     * @param String $password Password of the user
     * @return bool true if the user is logged in
     * @throws \PDOException
     */
    public static function login($username, $password)
    {
        $user = User::findByUsername($username);
        if (password_verify($password, $user->getPassword())) {
            if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
                $user->password = password_hash($password, PASSWORD_DEFAULT);
                $user->update();
            }
            $_SESSION['user'] = $user;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Insert this User into the database.
     * @throws \InvalidArgumentException thrown when information is missing or the user is a visitor
     * @throws \PDOException
     */
    public function insert()
    {
        $db = Base::getConnection();

        if (self::isAValidUser() || self::isVisitor()) {
            throw new \InvalidArgumentException();
        }

        $stmt = $db->prepare("INSERT INTO users(username, password, email) VALUES(:name, :pass, :email);");
        $stmt->bindParam(":name", $this->username, PDO::PARAM_STR);
        $stmt->bindParam(":pass", $this->password, PDO::PARAM_STR);
        $stmt->bindParam(":email", $this->email, PDO::PARAM_STR);

        $stmt->execute();

        $this->id = $db->LastInsertID('users');
        $stmt->closeCursor();
    }

    /**
     * Update the user in the database
     * @throws \InvalidArgumentException thrown when information is missing or the user is a visitor
     * @throws \PDOException
     */
    public function update()
    {
        $db = Base::getConnection();

        if (self::isAValidUser() || self::isVisitor() || isset($this->id) || $this->id < 1) {
            throw new \InvalidArgumentException();
        }

        $stmt = $db->prepare("UPDATE users SET username=:name, email=:email, password=:pass WHERE user_id =:id");
        $stmt->bindParam(":name", $this->username, PDO::PARAM_STR);
        $stmt->bindParam(":pass", $this->password, PDO::PARAM_STR);
        $stmt->bindParam(":email", $this->email, PDO::PARAM_STR);
        $stmt->bindParam(":id", $this, PDO::PARAM_INT);

        $stmt->execute();

        $stmt->closeCursor();
    }


    /**
     * Indicate if the user is a valid user
     * He must have a username, password, email
     * @return bool true if it's a valid user
     */
    public function isAValidUser()
    {
        return !isset($this->username) || empty($this->username) ||
        !isset($this->email) || empty($this->email) ||
        !isset($this->password) || empty($this->password);
    }
}