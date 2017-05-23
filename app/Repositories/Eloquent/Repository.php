<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\Repository as RepositoryInterface;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var Model
     */
    private $model;

    protected $modelClass = null;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    public function makeModel()
    {
        if(!$this->modelClass) {
            throw new \Exception('Model class must be set');
        }

        $model = $this->app->make($this->modelClass);

        if(!$model instanceof Model) {
            throw new \Exception(
                'Class ' .$this->modelClass. ' must be an instance of Illuminate\\Databse\\Eloquent\\MOdel'
            );
        }

        $this->model = $model;
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 10, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     * @param int $id
     * @return boolean
     */
    public function save(array $data, $id = 0)
    {
        $item = $this->makeModel();

        if($id) {
            $item = $this->model->find($id);
        }

        foreach ($data as $k => $v) {
            $item[$k] = $v;
        }

        return $item->save();
    }

    /**
     * @param int $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*'])
    {
        return $this->model->where($field, '=', $value)->first($columns);
    }

    /**
     * @param int $id
     * @return boolean
     */
    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}