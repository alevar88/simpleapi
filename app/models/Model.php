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

namespace app\models;

use app\services\ContainerInterface;
use app\services\DatabaseInterface;

/**
 * Class Model
 * @package app\models
 */
abstract class Model
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $pkey;

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var DatabaseInterface
     */
    protected $db;

    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * Model constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->db->setTable($this->table);
        $this->db->setPrimaryKey($this->pkey ?? 'id');
        $this->container = $container;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function __get(string $key)
    {
        return $this->get($key) ?? null;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function __set(string $key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return $this->has($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return (in_array($key, $this->fields));
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return ($this->has($key)) ? $this->{$key} : null;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value)
    {
        if ($this->has($key)) {
            $this->{$key} = $value;
        }
    }

    /**
     * @return bool
     */
    public function isLoaded(): bool
    {
        return $this->isLoaded;
    }

    /**
     * @param array $params
     */
    private function makeProperties(array $params = array())
    {
        if (!empty($this->fields)) {
            foreach ($this->fields as $key) {
               if (in_array($key, array_keys($params))) {
                   $this->{$key} = $params[$key] ?? null;
               }
            }
        }
    }

    /**
     * @param array $params
     * @return ModelsCollection
     */
    public function loadAll(array $params = array()): ModelsCollection
    {
        if (empty($params)) {
            $params = $this->container->get('request')->getParams();
        }
        $this->makeProperties($params);
        $collection = new ModelsCollection;
        $results = $this->db->get($this->toArray());
        if (!empty($results)) {
            foreach ($results as $result) {
                foreach ($result as $key => $value) {
                    $this->{$key} = $value;
                }
                $this->isLoaded = true;
                $collection->addToCollection($this);
            }
        } else {
            $collection->addToCollection($this);
        }
        return $collection;
    }

    /**
     * @param array $params
     * @return Model|null
     */
    public function load(array $params = array())
    {
        return $this->loadAll($params)->getFirstModel();
    }

    /**
     * @param array $keys
     * @return array
     */
    public function toArray(array $keys = array()): array
    {
        $vars = get_object_vars($this);
        $result = array();
        foreach ($vars as $key => $value) {
            if (in_array($key, $this->fields)) {
                if (!empty($keys) && !in_array($key, $keys)) {
                    continue;
                }
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * @return void
     */
    public function reset()
    {
        foreach ($this->fields as $key) {
            $this->{$key} = null;
        }
    }

    /**
     * @return void
     */
    public function unload()
    {
        $this->isLoaded = false;
    }

    /**
     * @return int
     */
    public function save(): int
    {
        if ($this->isLoaded) {
            return $this->db->update($this->toArray());
        }
        return $this->db->insert($this->toArray());
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        if ($this->isLoaded) {
            $result = $this->db->delete($this->toArray());
            $this->reset();
        }
        return $result ?? false;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function import(array $params): Model
    {
        foreach ($params as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }

}