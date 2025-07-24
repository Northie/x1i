<?php

namespace services\data;

class proxy implements iAdapter {
    private $adapter;

    public function __construct(iAdapter $adapter) {
        $this->adapter = $adapter;
    }

    public function setAdapter(iAdapter $adapter) {
        $this->adapter = $adapter;
        return $this;
    }

    public function getAdapter() {
        return $this->adapter;
    }

    public function create($data, $id = false) {
        return $this->adapter->create($data, $id);
    }

    public function read($data) {
        return $this->adapter->read($data);
    }

    public function readType($type, $options = []) {
        if (method_exists($this->adapter, 'readType')) {
            return $this->adapter->readType($type, $options);
        }
        return [];
    }

    public function update($data, $conditions = false) {
        return $this->adapter->update($data, $conditions);
    }

    public function delete($data, $conditions = false) {
        return $this->adapter->delete($data, $conditions);
    }

    public function query($query, $parameters = false) {
        return $this->adapter->query($query, $parameters);
    }
}
