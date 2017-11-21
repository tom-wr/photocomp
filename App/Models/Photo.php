<?php

namespace App\Models;
use Core\Model;
use PDO;

/**
 * Class Photo
 * The photo model
 * @package App\Models
 */
class Photo extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * Get all the photos in the database
     * @return array
     */
    public static function getAll()
    {
        try {
            $db = static::getDB();
            $stmt = $db->query('SELECT p.id, p.uid, p.filename, p.caption, p.created_at, u.username FROM photos p INNER JOIN users u ON p.uid=u.id'
                .' ORDER BY p.created_at DESC');
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;

        } catch (\PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Searches the database for keywords in the caption and in a given data range
     * @param $search_params [] should contain 'search' string for searching the captions and 'date-start' and 'date-end'
     * for filtering by date
     * @return array
     */
    public static function search($search_params)
    {
        try {
            $db = static::getDB();

            $sql = 'SELECT p.id, p.uid, p.filename, p.caption, p.created_at, u.username FROM photos p INNER JOIN users u ON p.uid=u.id'
                    .' WHERE p.caption LIKE :search AND p.created_at BETWEEN :start_date AND :end_date ORDER BY p.created_at DESC';

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':search' => '%' . $search_params['search'] . '%',
                ':start_date' => $search_params['date-start'],
                ':end_date' => $search_params['date-end']
            ]);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;

        } catch (\PDOException $e) {
            $e->getMessage();
        }
    }

    /**
     * Save a photo to the database
     * @return bool
     */
    public function save()
    {
        if(empty($this->errors)) {

            $sql = 'INSERT INTO photos(uid, filename, caption)
                VALUES (:uid, :filename, :caption)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $outcome = $stmt->execute([
                ':uid' => $this->uid,
                ':filename' => $this->filename,
                ':caption' => $this->caption
            ]);
            return $outcome;
        }
        return false;
    }

    /**
     * Find a single photo by a given id
     * @param $id
     * @return mixed
     */
    public static function findById($id)
    {
        $sql = 'SELECT * FROM photos WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

}