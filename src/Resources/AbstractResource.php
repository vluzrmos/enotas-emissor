<?php

namespace Vluzrmos\Enotas\Resources;

use ArrayAccess;
use JsonSerializable;
use Vluzrmos\Enotas\Client\Enotas;

abstract class AbstractResource implements JsonSerializable, ArrayAccess
{
    protected Enotas $enotas;

    protected $original = [];
    protected $attributes = [];

    protected $endpoint = null;

    protected $orderField = 'createdAt';

    public function __construct(array $attributes = [])
    {
        $this->original = $attributes;
        $this->fill($attributes);
    }

    public function setEnotas(Enotas $enotas)
    {
        $this->enotas = $enotas;

        return $this;
    }

    public function getEnotas()
    {
        return $this->enotas ?: Enotas::getInstance();
    }

    public function fill(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->setAttributeValue($key, $value);
        }

        return $this;
    }

    public function mapIn(array $data = [])
    {
        $items = [];

        foreach ($data as $attributes) {
            $items[] = $this->newResource((array) $attributes);
        }

        if (class_exists('\Illuminate\Support\Collection')) {
            return new \Illuminate\Support\Collection($items);
        }

        return $items;
    }

    public function newResource(array $attributes = [])
    {
        $resource = new static($attributes);

        $resource->setEnotas($this->getEnotas());

        return $resource;
    }

    public function find($id)
    {
        $data = $this->getEnotas()->request("GET", "{$this->endpoint}/{$id}");

        return $this->newResource((array) $data);
    }

    public function all($pageNumber = 0, $pageSize = 999, $orderBy = null, $filter = null)
    {
        $response = $this->getEnotas()->request("GET", "{$this->endpoint}/getFilterBy", [
            "query" => compact('pageNumber', 'pageSize', 'orderBy', 'filter')
        ]);

        if (empty($response['totalRecords']) || empty($response['data'])) {
            return $this->mapIn([]);
        }

        return $this->mapIn((array) $response['data']);
    }

    public function first()
    {
        $items = $this->all(0, 1, "{$this->orderField} asc");

        if (empty($items)) {
            return null;
        }

        return $items[0];
    }

    public function last()
    {
        $items = $this->all(0, 1, "{$this->orderField} desc");

        if (empty($items)) {
            return null;
        }
        
        return $items[0];
    }

    public function create($data = [])
    {
        $newInstance = $this->newResource($data);

        $saved = $newInstance->save();

        if (!$saved) {
            return null;
        }

        return $newInstance;
    }

    public function exists()
    {
        return (bool) $this->id;
    }

    public function getId()
    {
        return $this->getAttributeValue('id') ?: $this->getAttributeValue($this->getFullResourceIdName());
    }

    public function getDirtyWithId()
    {
        $dirty = $this->getDirty();

        if (!$dirty) {
            return $dirty;
        }

        return $this->exists() ? array_merge($dirty, ['id' => $this->id]) : $dirty;
    }

    public function save()
    {
        $dirty = $this->getDirtyWithId();

        if (!$dirty) {
            return true;
        }

        $data = $this->getEnotas()->request('POST', $this->endpoint, [
            'json' => $dirty
        ]);

        if (empty($data)) {
            return false;
        }

        foreach ($data as $key => $value) {
            $this->setAttributeValue($key, $value);
        }

        $id = $this->getAttributeValue($this->getFullResourceIdName());

        if ($id) {
            $this->setAttributeValue('id', $id);
        }

        $this->syncOriginal();

        return true;
    }

    public function syncOriginal()
    {
        $this->original = $this->attributes;

        return $this;
    }

    public function getFullResourceIdName()
    {
        $basename = mb_strtolower(basename(str_replace(['/\\'], DIRECTORY_SEPARATOR, __CLASS__)));

        return "{$basename}Id";
    }

    public function getAttributeValue($key, $default = null)
    {
        if ($this->hasAttribute($key)) {
            return $this->attributes[$key];
        }

        if (is_callable($default)) {
            return $default();
        }

        return $default;
    }

    public function setAttributeValue($key, $value)
    {
        $this->attributes[$key] = $value;

        return  $this;
    }

    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    public function hasOriginal($key)
    {
        return isset($this->original[$key]);
    }

    public function isDirty($key)
    {
        if (!$this->exists()) {
            return true;
        }

        return $this->getOriginal($key) !== $this->getAttributeValue($key);
    }

    public function getOriginal($key)
    {
        if (isset($this->original[$key])) {
            return $this->original[$key];
        }

        return null;
    }

    public function getDirty()
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (!$this->isDirty($key)) {
                continue;
            }

            $dirty[$key] = $value;
        }

        return $dirty;
    }

    public function __get($key)
    {
        return $this->getAttributeValue($key);
    }

    public function __set($key, $value)
    {
        $this->setAttributeValue($key, $value);
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function offsetExists($offset)
    {
        return $this->hasAttribute($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getAttributeValue($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->setAttributeValue($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}
