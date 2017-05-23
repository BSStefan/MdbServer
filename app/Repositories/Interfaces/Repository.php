<?php

    namespace App\Repositories\Interfaces;

    interface Repository
    {
        public function all($columns);

        public function paginate($perPage, $columns);

        public function save(array $data, $id);

        public function delete($id);

        public function find($id, $columns);

        public function findBy($field, $value, $columns);
    }