<?php


namespace Models;

use PDO;

/**
 * Represent a Playlist
 * @package Models
 */
class Playlist
{

    /**
     * @var int id of the user
     */
    private $user_id;

    /**
     * @var int id of the playlist
     */
    private $playlist_id;

    /**
     * @var string name of the paylist
     */
    private $playlist_name;

    /**
     * Return the playlist id
     * @return int id of the playlist
     */
    public function getPlaylistId()
    {
        return $this->playlist_id;
    }

    /**
     * Set the playlist id
     * @param int $playlist_id
     */
    public function setPlaylistId($playlist_id)
    {
        $this->playlist_id = $playlist_id;
    }

    /**
     * Return the name of the playlist
     * @return string name of the playlist
     */
    public function getPlaylistName()
    {
        return $this->playlist_name;
    }

    /**
     * Set the name of the playlist
     * htmlspecialchars is used in this function
     * @param string $playlist_name
     * @throws \InvalidArgumentException
     */
    public function setPlaylistName($playlist_name)
    {
        if (!self::isValidPlaylistName($playlist_name)) {
            throw new \InvalidArgumentException();
        }
        $this->playlist_name = htmlspecialchars($playlist_name);
    }

    /**
     * Return the user id
     * @return int user id
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set the user id of the playlist
     * @param int $user_id user id
     * @throws \PDOException
     * @throws \InvalidArgumentException the user does not exist
     */
    public function setUserId($user_id)
    {
        /* 
        //if (!User::findByID($user_id)) {
            throw new \InvalidArgumentException();
        }*/
        $this->user_id = $user_id;
    }

    /**
     * Indicate if the name is valid or not
     * @param string $name name of the playlist
     * @return bool true is it's correct
     */
    public static function isValidPlaylistName($name)
    {
        return isset($name) && strlen($name) > 1 && strlen($name) < 60;
    }

    /**
     * Retrieve all Playlist in the database
     * @return Playlist[] List of playlist
     * @throws \PDOException
     */
    public static function findAll()
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM playlists ORDER BY user_id, playlist_name;");
        $stmt->execute();

        $tab = array();
        foreach ($stmt->fetchALL() as $plist) {
            $pl = new Playlist();
            $pl->playlist_id = $plist['playlist_id'];
            $pl->user_id = $plist['user_id'];
            $pl->playlist_name = $plist['playlist_name'];

            $tab[$plist['playlist_id']] = $pl;
        }
        $stmt->closeCursor();
        return $tab;
    }

    /**
     * Retrieves a Playlist through his playlist_id.
     * @param integer $id id of the playlist
     *
     * @return Playlist|false The playlist or false if not found
     * @throws \PDOException
     */
    public static function findByPlaylistID($id)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM playlists WHERE playlist_id=:id;");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $response = $stmt->fetch();
        if ($response) {
            $pl = new Playlist();
            $pl->playlist_id = $response['playlist_id'];
            $pl->user_id = $response['user_id'];
            $pl->playlist_name = $response['playlist_name'];
            $stmt->closeCursor();
            return $pl;
        } else {
            return false;
        }
    }

    /**
     * Retrieve a list of Playlist through user_id
     * @param integer $id id of the playlist
     *
     * @return Playlist[] list of playlist
     * @throws \PDOException
     */
    public static function findByUserID($id)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM playlists WHERE user_id=:id;");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $tab = array();
        foreach ($stmt->fetchALL() as $plist) {
            $pl = new Playlist();
            $pl->playlist_id = $plist['playlist_id'];
            $pl->user_id = $plist['user_id'];
            $pl->playlist_name = $plist['playlist_name'];

            $tab[$plist['playlist_id']] = $pl;
        }
        $stmt->closeCursor();

        return $tab;
    }


    /**
     * Insert this playlist into the database.
     * @throws \InvalidArgumentException thrown when information is missing
     * @throws \PDOException
     */
    public function insert()
    {
        $db = Base::getConnection();
        //var_dump(User::findByID($this->user_id));
        if (!self::isValidPlaylistName($this->playlist_name) || User::findByID($this->user_id) == false) {
            throw new \InvalidArgumentException();
        }

        $stmt = $db->prepare("INSERT INTO playlists(user_id, playlist_name) VALUES( :user_id, :name);");
        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $this->playlist_name, PDO::PARAM_STR);

        $ok = $stmt->execute();

        $this->playlist_id = $db->LastInsertID();
        $stmt->closeCursor();
        
        return $ok;
    }

    /**
     * Update the playlist in the database
     * @throws \InvalidArgumentException thrown when information is missing
     * @throws \PDOException
     */
    public function update()
    {
        $db = Base::getConnection();

        if (self::isValidPlaylistName($this->playlist_name) || !User::findByID($this->user_id)) {
            throw new \InvalidArgumentException();
        }

        $stmt = $db->prepare("UPDATE playlists SET playlist_name=:name, user_id=:user_id WHERE playlist_id=:playlist_id;");
        $stmt->bindParam(":name", $this->playlist_name, PDO::PARAM_STR);
        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(":playlist_id", $this->playlist_id, PDO::PARAM_INT);

        $stmt->execute();

        $stmt->closeCursor();
    }

}