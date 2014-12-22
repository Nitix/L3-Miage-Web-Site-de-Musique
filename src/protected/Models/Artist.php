<?php


namespace Models;

use PDO;

/**
 * Represent an artist
 * @package Models
 */
class Artist
{

    /**
     * @var int Id of the artist
     */
    private $id;

    /**
     * @var string name of the artist
     */
    private $name;

    /**
     * @var string url of the image
     */
    private $image_url;

    /**
     * @var string information about the user
     */
    private $info;

    /**
     * The id to set
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * The name of the artist
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Image of the artist
     * @param string $image_url
     */
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
    }

    /**
     * Information of the artist
     * @param string $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * Return the id of the artist
     * @return int id of the artist
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the url image of the artist
     * @return string url image of the artist
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * Return the information about the user
     * @return string information about the user
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Return the name of the user
     * @return string name of the user
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieve all Artist in the database
     * @return Artist[] list of artist
     * @throws \PDOException
     */
    public static function findAll()
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM artists");
        $stmt->execute();

        $tab = array();
        foreach ($stmt->fetchALL() as $art) {
            $a = new Artist();
            $a->id = $art['artist_id'];
            $a->name = $art['name'];
            $a->image_url = $art['image_url'];
            $a->info = $art['info'];

            $tab[$art['artist_id']] = $a;
        }
        $stmt->closeCursor();
        return $tab;

    }

    /**
     * Retrieve an Artist through his id
     * @param integer $id id to search
     * @return Artist|false the artist found or null if not found
     * @throws \PDOException
     */
    public static function findByID($id)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM artists WHERE artist_id=:id ;");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $response = $stmt->fetch();

        if ($response) {
            $a = new Artist();
            $a->id = $response['artist_id'];
            $a->name = $response['name'];
            $a->image_url = $response['image_url'];
            $a->info = $response['info'];
            $stmt->closeCursor();
            return $a;
        } else {
            return false;
        }
    }

    /**
     * Retrieve an Artist through his name
     * @param string $name name to search
     * @return Artist|false the artist found or null if not found
     * @throws \PDOException
     */
    public static function findByName($name)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM artists WHERE name=:name ORDER BY name;");
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->execute(array($name));

        $response = $stmt->fetch();

        if ($response) {
            $a = new Artist();
            $a->name = $response['artist_name'];
            $a->name = $response['name'];
            $a->image_url = $response['image_url'];
            $a->info = $response['info'];
            $stmt->closeCursor();
            return $a;
        } else {
            return false;
        }
    }

    /**
     * Retrieves an array of all Artists which name's contains
     * a specified sequence of characters.
     * @param string $name The specified sequence of characters.
     * @param int $limit The number of artist to search
     * @return Artist[] array of all Artists which name's contains
     * a specified sequence of characters.
     * @throws \PDOException
     */
    public static function findByNameLike($name, $limit = 5)
    {
        $db = Base::getConnection();
        
        if($limit != 0) //Vraiment pas sûr,
            $qlimit = 'LIMIT :limit';
        else
            $qlimit = '';
        
        $stmt = $db->prepare("SELECT * FROM artists WHERE name LIKE :like ORDER BY name " . $qlimit . ";");
        $like = "%" . $name . "%";
        $stmt->bindParam(":like", $like, PDO::PARAM_STR);
        if($limit != 0)
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();


        $tab = array();
        foreach ($stmt->fetchALL() as $art) {
            $a = new Artist();
            $a->id = $art['artist_id'];
            $a->name = $art['name'];
            $a->image_url = $art['image_url'];
            $a->info = $art['info'];

            $tab[$art['artist_id']] = $a;
        }
        $stmt->closeCursor();
        return $tab;
    }


}