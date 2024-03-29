<?php


namespace Models;

use PDO;

/**
 * Represent a Track of a playlist, give his position
 * @package Models
 */
class PlaylistTrack
{

    /**
     * @var int id of the track
     */
    private $track_id;

    /**
     * @var int id of the playlist
     */
    private $playlist_id;

    /**
     * @var int position of the track in the playlist
     */
    private $position;

    /**
     * @return int
     */
    public function getPlaylistId()
    {
        return $this->playlist_id;
    }

    /**
     * Return the id of the playlist
     * @param int $playlist_id if of the playlist
     * @throws \InvalidArgumentException the playlist does not exist
     */
    public function setPlaylistId($playlist_id)
    {
        if (!Playlist::findByPlaylistID($playlist_id)) {
            throw new \InvalidArgumentException();
        }
        $this->playlist_id = $playlist_id;
    }

    /**
     * Return the position of the track int h
     * @return int position of the track
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getTrackId()
    {
        return $this->track_id;
    }

    /**
     * Set the track id
     * @param int $track_id id of the track
     * @throws \InvalidArgumentException the track does not exist
     */
    public function setTrackId($track_id)
    {
        if (!Track::findByTrackID($track_id)) {
            throw new \InvalidArgumentException();
        }
        $this->track_id = $track_id;
    }

    /**
     * Retrieves a list PlaylistTrack through his playlist_id
     * @param integer $id id of the playlist
     *
     * @return PlaylistTrack[] list of PlaylistTrack
     * @throws \PDOException
     */
    public static function findByPlaylistID($id)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM playlists_tracks WHERE playlist_id=:id ORDER BY position;");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $tab = array();
        foreach ($stmt->fetchAll() as $pl_trk) {
            $pt = new PlaylistTrack();

            $pt->playlist_id = $pl_trk['playlist_id'];
            $pt->track_id = $pl_trk['track_id'];
            $pt->position = $pl_trk['position'];

            $tab[$pl_trk['playlist_id']] = $pt;
        }

        $stmt->closeCursor();
        return $tab;
    }
    
    public static function findByPlaylistIDWithTracks($plid)
    {
        $db = Base::getConnection();

        $stmt = $db->prepare("SELECT * FROM playlists_tracks INNER JOIN tracks ON playlists_tracks.track_id = tracks.track_id INNER JOIN artists ON artists.artist_id = tracks.artist_id WHERE playlist_id=:id ORDER BY position;");
        $stmt->bindParam(":id", $plid, PDO::PARAM_INT);
        $ok = $stmt->execute();
        //var_dump($stmt);
        $tab = array();
        foreach ($stmt->fetchAll() as $pl_trk) {
            
            $tab[] = $pl_trk;
        }
        
        $stmt->closeCursor();
        return $tab;
    }

    public function insert()
    {
        $db = Base::getConnection();

        $plid = $this->getPlaylistId();
        $trid = $this->getTrackId();
        $pos = $this->getPosition();
        
        $stmt = $db->prepare("INSERT INTO playlists_tracks (playlist_id, track_id, position) VALUES (:playlist_id, :track_id, :position)");
        
        $stmt->bindParam(":playlist_id", $plid , PDO::PARAM_INT);
        $stmt->bindParam(":track_id", $trid , PDO::PARAM_INT);
        $stmt->bindParam(":position", $pos , PDO::PARAM_INT);
        $ok = $stmt->execute();
        
        return $ok;
    }

    public static function insertMultiples($tracks, $playlist_id){

        $query = "INSERT INTO playlists_tracks (playlist_id, track_id, position)  VALUES";

        $params = array();
        $position = 0;
        foreach($tracks as $track){
            $query .= " (?, ?, ?),";
            $params[] = $playlist_id;
            $params[] = $track['track_id'];
            $params[] = $position;
            $position++;
        }
        $query = rtrim($query, ',');

        $db = Base::getConnection();
        $stmt = $db->prepare($query);

        $stmt->execute($params);

    }
    
    public function delete()
    {
        $db = Base::getConnection();

        $plid = $this->getPlaylistId();
        $trid = $this->getTrackId();
        $pos = $this->getPosition();
        
        $stmt = $db->prepare("DELETE FROM playlists_tracks WHERE playlist_id=:plid AND position=:pos ; 
                                UPDATE playlists_tracks 
                                SET position = position - 1 
                                WHERE playlist_id = :plid AND position > :pos
                                ORDER BY position ASC");
        
        $stmt->bindParam(":plid", $plid , PDO::PARAM_INT);
        $stmt->bindParam(":pos", $pos , PDO::PARAM_INT);
        $ok = $stmt->execute();
        
        return $ok;
    }
} 