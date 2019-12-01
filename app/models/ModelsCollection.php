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

/**
 * Class ModelsCollection
 * @package app\models
 */
class ModelsCollection
{

    /**
     * @var Model[]
     */
    private $collection = array();
    
    /**
     * @param Model $model
     * @return void
     */
    public function addToCollection(Model $model)
    {
        $this->collection[] = $model;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return (empty($this->collection));
    }

    /**
     * @return void
     */
    public function destroyCollection()
    {
        $this->collection = array();
    }

    /**
     * @return Model|null
     */
    public function getFirstModel()
    {
        return $this->collection[0] ?? null;
    }

    /**
     * @param array $params
     * @return array
     */
    public function toArray(array $params = array()): array
    {
        $result = array();
        foreach ($this->collection as $key => $model) {
            if ($model->isLoaded()) {
                $result[] = $model->toArray($params);
            }
        }
        return $result;
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __call(string $name, $arguments = array())
    {
        foreach ($this->collection as $model) {
            if (method_exists($model, $name)) {
                call_user_func_array(array($model, $name), $arguments);
            }
        }
    }
    
}