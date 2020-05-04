<?php

namespace App\Model;

use libs\Db\Db;
use PDO;

/**
 * @package App\Modle
 */
class Task
{
    /**
     * Get all tasks
     *
     * @access public
     * @return array
     */
    public function getTasks(): array
    {
        $query = "SELECT u.username, u.email, t.id, t.text, t.status, t.is_updated AS updated " . 
                 "FROM user AS u INNER JOIN task AS t ON u.id = t.user_id";

        $stmt = Db::query($query)->fetchAll(PDO::FETCH_ASSOC);

        return $stmt ;
    }

    /**
     * Get single task
     *
     * @param integer $id
     * @return array
     */
    public function getTask(int $id): array
    {
        $query = "SELECT text, status FROM task WHERE id = :id";

        $stmt = Db::prepare($query, [':id' => $id])->fetch(PDO::FETCH_ASSOC);

        return $stmt ? $stmt : array();
    }

    /**
     * Create new task
     *
     * @param string $text
     * @param integer $user_id
     * @return void
     */
    public function createTask(string $text, int $user_id)
    {
        $query = "INSERT INTO task (`text`, `status`, `user_id`) VALUES (:text, :status, :user_id)";

        $stmt = Db::prepare($query, [
            ':text' => $text,
            ':status' => 'created',
            ':user_id' => $user_id
        ]);

        return $stmt;
    }

    /**
     * Edit task
     *
     * @param string $text
     * @param integer $id
     * @return void
     */
    public function editTask(string $text, int $id)
    {
        $query = "UPDATE task SET text = :text, is_updated = :is_updated WHERE id = :id";

        $stmt = Db::prepare($query, [
            ':text' => $text,
            ':is_updated' => 1,
            ':id' => $id
        ]);

        return $stmt;
    }

    /**
     * Cnage status task
     *
     * @param integer $id
     * @return void
     */
    public function changeStatus(int $id)
    {
        $query = "UPDATE task SET status = :status WHERE id = :id";

        $stmt = Db::prepare($query, [
            ':status' => 'completed',
            ':id' => $id
        ]);

        return $stmt;
    }
}