<?php
/**
 * This file is part of the "Simple RESTful-API PHP skeleton"
 *
 * @author Alexander Varnikov <alevar88@gmail.com>
 *
 * Project home: https://github.com/alevar88/simpleapi
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @copyright Copyright 2019, Alexander Varnikov <alevar88@gmail.com>, Alexander Groza <ametiloray@gmail.com>
 *
 */

declare(strict_types = 1);

namespace app\services;

/**
 * Class Database
 * @package app\services
 */
class Database implements DatabaseInterface
{

    /**
     * @var \PDO
     */
    private $db;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $pkey = 'id';

    /**
     * Database constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        try {
            $this->db = new \PDO($config['dsn'], $config['username'], $config['password'], $config['options']);
            if (is_callable($config['onConnect'] ?? null)) {
                call_user_func($config['onConnect'], $this->db);
            }
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * @param string $table
     */
    public function setTable(string $table)
    {
        $this->table = $table;
    }

    /**
     * @param string $pkey
     */
    public function setPrimaryKey(string $pkey)
    {
        $this->pkey = $pkey;
    }

    /**
     * @return \PDO
     */
    public function pdo(): \PDO
    {
        return $this->db;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getPlaceholder(array $params): array
    {
        $placeholder = array(array(), array());
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $placeholder[0][] = "`{$key}` IN (" . rtrim(str_repeat('?,', count($value)), ',') . ")";
                foreach ($value as $item) {
                    $placeholder[1][] = $item;
                }
            } else {
                $placeholder[0][] = "`{$key}` = ?";
                $placeholder[1][] = $value;
            }
        }
        return $placeholder;
    }

    /**
     * @param array $params
     * @return array
     */
    public function get(array $params): array
    {
        list($slots, $inputs) = $this->getPlaceholder($params);
        $sql = sprintf("SELECT * FROM `{$this->table}` WHERE %s", join(' AND ', $slots));
        $stmt = $this->db->prepare($sql);
        if ($stmt instanceof \PDOStatement) {
            $stmt->execute($inputs);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $result ?? array();
    }

    /**
     * @param array $params
     * @return int
     */
    public function insert(array $params): int
    {
        if (in_array($this->pkey, array_keys($params))) {
            unset($params[$this->pkey]);
        }
        list($slots, $inputs) = $this->getPlaceholder($params);
        $sql = sprintf("INSERT INTO `{$this->table}` SET %s", join(', ', $slots));
        $stmt = $this->db->prepare($sql);
        if ($stmt instanceof \PDOStatement) {
            $stmt->execute($inputs);
            $result = intval($this->db->lastInsertId());
        }
        return $result ?? 0;
    }

    /**
     * @param array $params
     * @return int
     */
    public function update(array $params): int
    {
        if (isset($params[$this->pkey])) {
            $pkey = $params[$this->pkey];
            unset($params[$this->pkey]);
            list($slots, $inputs) = $this->getPlaceholder($params);
            array_push($inputs, $pkey);
            $sql = sprintf("UPDATE `{$this->table}` SET %s WHERE %s = ? LIMIT 1", join(', ', $slots), $this->pkey);
            $stmt = $this->db->prepare($sql);
            if ($stmt instanceof \PDOStatement) {
                if ($stmt->execute($inputs)) {
                    $result = intval($pkey);
                }
            }
        }
        return $result ?? 0;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function delete(array $params): bool
    {
        if (isset($params[$this->pkey])) {
            $stmt = $this->db->prepare(sprintf("DELETE FROM `{$this->table}` WHERE %s = ? LIMIT 1", $this->pkey));
            if ($stmt instanceof \PDOStatement) {
                $result = $stmt->execute(array($params[$this->pkey]));
            }
        }
        return $result ?? false;
    }

}