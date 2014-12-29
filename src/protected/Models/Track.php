<?php


namespace Models;

use PDO;

/**
 * Represent a Track in the database
 * @package Models
 */
class Track
{

    /**
     * @var int Id of the track
     */
    private $track_id;

    /**
     * @var int Id of the artist
     */
    private $artist_id;

    /**
     * @var string title of the music
     */
    private $title;

    /**
     * @var string url of the music
     */
    private $mp3_url;

    /**
     * @var Artist artist of the track
     */
    private $artist;

    /**
     * Return the artist Id ot the track
     * @return mixed id of the artist
     */
    public function getArtistId()
    {
        return $this->artist_id;
    }

    /**
     * Return the mp3 url of the track
     * @return string mp3 url of the track
     */
    public function getMp3Url()
    {
        return $this->mp3_url;
    }

    /**
     * Return the title of the track
     * @return string title of the track
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return the id of the track
     * @return int if of the track
     */
    public function getTrackId()
    {
        return $this->track_id;
    }

    /**
     * Return the artist of the track
     * @return null|Artist Artist or null
     */
    public function getArtist()
    {
        return $this->artist;
    }


    /**
     * Retrieve all Tracks in the database
     * @return Track[] List of tracks
     * @throws \PDOException
     */
    public static function findAll()
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM tracks");
        $stmt->execute();

        $tab = array();
        foreach ($stmt->fetchALL() as $trk) {
            $t = new Track();

            $t->track_id = $trk['track_id'];
            $t->artist_id = $trk['artist_id'];
            $t->title = $trk['title'];
            $t->mp3_url = $trk['mp3_url'];

            $tab[$trk['track_id']] = $t;
        }
        $stmt->closeCursor();
        return $tab;
    }

    /**
     * Retrieve an Track through his id.
     * @param int $id Id of the track
     * @return Track|null The track or null if not found
     * @throws \PDOException
     */
    public static function findByTrackID($id)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM tracks WHERE track_id=:id ;");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute(array($id));

        $response = $stmt->fetch();

        if ($response) {
            $t = new Track();
            $t->track_id = $response['track_id'];
            $t->artist_id = $response['artist_id'];
            $t->title = $response['title'];
            $t->mp3_url = $response['mp3_url'];

            $stmt->closeCursor();
            return $t;
        } else {
            return false;
        }
    }

    public static function findByArtistID($id)
    {
       $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM tracks WHERE artist_id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();


        $tab = array();
        foreach ($stmt->fetchALL() as $response) {
            $t = new Track();
            $t->track_id = $response['track_id'];
            $t->artist_id = $response['artist_id'];
            $t->title = $response['title'];
            $t->mp3_url = $response['mp3_url'];

            $tab[$response['track_id']] = $t;
        }
        $stmt->closeCursor();
        return $tab;
    }

    /**
     * Retrieve an Track through his title.
     * @param string $title Title of the track
     * @return Track|null The track or null if not found
     * @throws \PDOException
     */
    public static function findByTitle($title)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM tracks WHERE title=:title ;");
        $stmt->bindParam(":title", $title, PDO::PARAM_INT);
        $stmt->execute(array($title));

        $response = $stmt->fetch();

        if ($response) {
            $t = new Track();
            $t->track_id = $response['track_id'];
            $t->artist_id = $response['artist_id'];
            $t->title = $response['title'];
            $t->mp3_url = $response['mp3_url'];

            $stmt->closeCursor();
            return $t;
        } else {
            return false;
        }
    }

    /**
     * Retrieves an array of all Artists which name's contains
     * a specified sequence of characters.
     * @param string $name The specified sequence of characters.
     * @param int $limit the number of track to search
     * @return Track[] array of all Tracks which name's contains
     * a specified sequence of characters.
     * @throws \PDOException
     */
    public static function findByNameLike($name, $limit = 5)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM tracks WHERE title LIKE :like ORDER BY title LIMIT :limit");
        $like = "%" . $name . "%";
        $stmt->bindParam(":like", $like, PDO::PARAM_STR);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();


        $tab = array();
        foreach ($stmt->fetchALL() as $response) {
            $t = new Track();
            $t->track_id = $response['track_id'];
            $t->artist_id = $response['artist_id'];
            $t->title = $response['title'];
            $t->mp3_url = $response['mp3_url'];

            $tab[$response['track_id']] = $t;
        }
        $stmt->closeCursor();
        return $tab;
    }

    /**
     * Retrieves an array of all Artists which name's contains
     * a specified sequence of characters.
     * @param string $name The specified sequence of characters.
     * @param int $limit the number of track to search
     * @return Track[] array of all tracks which name's contains
     * a specified sequence of characters, it give also artist
     * @throws \PDOException
     */
    public static function findByNameLikeWithArtist($name, $limit = 20)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT tracks.*, artists.artist_id, artists.name, artists.image_url FROM tracks INNER JOIN artists ON tracks.artist_id = artists.artist_id  WHERE tracks.title LIKE :like OR artists.name LIKE :like ORDER BY tracks.title LIMIT :limit");
        $like = "%" . $name . "%";
        $stmt->bindParam(":like", $like, PDO::PARAM_STR);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();


        $tab = array();
        foreach ($stmt->fetchALL() as $response) {
            $t = new Track();
            $t->track_id = $response['track_id'];
            $t->artist_id = $response['artist_id'];
            $t->title = $response['title'];
            $t->mp3_url = $response['mp3_url'];

            $a = new Artist();
            $a->setId($response['artist_id']);
            $a->setImageUrl($response['image_url']);
            $a->setName($response['name']);

            $t->artist = $a;
            $tab[$response['track_id']] = $t;
        }
        $stmt->closeCursor();
        return $tab;
    }
}