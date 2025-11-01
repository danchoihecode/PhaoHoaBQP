<?php

namespace App\Main\BaseResponse;

use Dotenv\Repository\RepositoryInterface;
use Illuminate\Support\Facades\App;
use function PHPUnit\Framework\isEmpty;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;
    protected $query;
    protected $skipCriteria = false;
    protected $criteria;

    public function __construct()
    {
        $this->setModel();
    }

    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    abstract public function getModel();

    /**
     * @var array
     */
    private $allowed_operator = ['>', '>=', '=', '!=', '<>', '<', '<=', 'like', 'not like', 'in', 'not in', 'Null', 'NotNull'];

    /**
     * @var array
     */
    private $allowed_order = ["asc", "desc"];

    /**
     * @var App
     */
    private $app;


    public function all()
    {
        $this->getModel();

        return $this->model->all();
    }

    public function findWhere(array $condition = [], array $columns = ['*'], int $limit = 20, int $offset = 0, array $orderBy = [])
    {
        //reset model
        $this->getModel();

        $this->addCondition($condition);
        if ($offset) {
            $this->model = $this->model->offset($offset);
        }
        if ($limit) {
            $this->model = $this->model->limit($limit);
        }
        $this->orderBy($orderBy);
        $result = $this->model->get($columns);
        if ($result && count($result) > 0) {
            return $result;
        } else {
            return array();
        }
    }

    public function find($id, array $columns = ['*'])
    {
        //reset model
        $this->getModel();

        return $this->model->find($id, $columns);
    }


    public function findOne($attribute, $value, array $columns = ['*'])
    {
        //reset model
        $this->getModel();

        return $this->model->where($attribute, "=", $value)->first($columns);
    }

    public function findOneLast($attribute, $value, array $columns = ['*'])
    {
        //reset model
        $this->getModel();

        return $this->model->where($attribute, "=", $value)->orderBy('id', 'desc')->first($columns);
    }

    public function insert(array $data)
    {
        //reset model
        $this->getModel();

        return $this->model->insert($data);
    }

    public function countWhere(array $condition = [])
    {
        //reset model
        $this->getModel();

        $this->addCondition($condition);
        return $this->model->count();
    }

    public function count()
    {
        //reset model
        $this->getModel();

        return $this->model->count();
    }

    public function update($attribute, $value, array $data)
    {
        //reset model
        $this->getModel();
        //update
        $this->model->where($attribute, '=', $value)->update($data);

        return $this->model->where($attribute, '=', $value)->get();
    }

    public function updateOrCreate($id, array $data)
    {
        $this->getModel();
        return $this->model->updateOrCreate(['id' => $id], $data);
    }

    public function updateWhere(array $condition = [], array $data)
    {
        //reset model
        $this->getModel();

        $this->addCondition($condition);
        return $this->model->update($data);
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 1, array $columns = ['*'])
    {
        //reset model
        $this->getModel();

        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        //reset model
        $this->getModel();

        return $this->model->create($data);
    }

    public function createMulti(array $data)
    {
        //reset model
        $this->getModel();
        return $this->model->insert($data);
    }

    public function delete($id)
    {
        //reset model
        $this->getModel();
        return $this->model->destroy($id);
    }

    public function destroy(array $params)
    {
        $delete = [];
        for ($i = 0; $i < count($params); $i++) {
            $delete[$i] = $params[$i];
        }
        //gets
        $query = $this->model;
        $query->whereRaw(
            implode(' and ', $delete)
        );
        $data = $query->delete();

        return [
            'data' => $data,
            'message' => 'success',
        ];
    }


    public function deleteWhere($attribute, $value)
    {

        //reset model
        $this->getModel();

        return $this->model->where($attribute, '=', $value)->delete();
    }



    /**
     * @param array $conditions
     * @return boolean
     */
    private function validateCondition(array $conditions = [])
    {
        foreach ($conditions as $condition) {
            if (!is_array($condition) || count($condition) != 3 || !isset($condition[0]) || !isset($condition[1]) || !isset($condition[2])) {
                die("condition error");
            }

            $attribute = $condition[0];
            $operator = $condition[1];

            if (!in_array($operator, $this->allowed_operator)) {
                die("condition error");
            }
        }

        return true;
    }

    private function validateOrderBy(array $orderBy = [])
    {
        $check = true;
        if (!$orderBy || !is_array($orderBy)) {
            $check = false;
        }

        if (!isset($orderBy[0]) || !isset($orderBy[1])) {
            $check = false;
        }

        $order = isset($orderBy[1]) ? $orderBy[1] : '';
        if (!in_array($order, $this->allowed_order)) {
            $check = false;
        }

        return $check;
    }

    protected function orderBy(array $orderBys = [])
    {

        //$orderBy is a empty array
        if (!$orderBys || !is_array($orderBys)) {
            return $this->model;
        }

        if (!isset($orderBys[0]) || !is_array($orderBys[0])) {
            $orderBys = [
                0 => $orderBys,
            ];
        }

        foreach ($orderBys as $orderBy) {
            $check = $this->validateOrderBy($orderBy);
            if (!$check) {
                continue;
            }
            $attribute = $orderBy[0];
            $order = $orderBy[1];
            $this->model = $this->model->orderBy($attribute, $order);
        }

        return $this->model;
    }

    /**
     * @param array $conditions
     * @return bool|mixed|null
     */
    protected function addCondition(array $conditions = [])
    {
        $this->validateCondition($conditions);

        foreach ($conditions as $condition) {

            $attribute = $condition[0];
            $operator = $condition[1];
            $value = $condition[2];
            if ($operator == "=") {
                $this->model = $this->model->where($attribute, "=", $value);
            }

            if ($operator == ">") {
                $this->model = $this->model->where($attribute, ">", $value);
            }

            if ($operator == ">=") {
                $this->model = $this->model->where($attribute, ">=", $value);
            }

            if ($operator == "<") {
                $this->model = $this->model->where($attribute, "<", $value);
            }

            if ($operator == "<=") {
                $this->model = $this->model->where($attribute, "<=", $value);
            }

            if ($operator == "<>") {
                $this->model = $this->model->where($attribute, "<>", $value);
            }

            if ($operator == "!=") {
                $this->model = $this->model->where($attribute, "!=", $value);
            }

            if ($operator == "in") {
                $this->model = $this->model->whereIn($attribute, $value);
            }

            if ($operator == "not int") {
                $this->model = $this->model->whereNotIn($attribute, $value);
            }

            if ($operator == "like") {
                $this->model = $this->model->where($attribute, "like", $value);
            }

            if ($operator == "not like") {
                $this->model = $this->model->where($attribute, "not like", $value);
            }

            if ($operator == "Null") {
                $this->model = $this->model->whereNull($attribute);
            }

            if ($operator == "NotNull") {
                $this->model = $this->model->whereNotNull($attribute);
            }
        }

        return $this->model;
    }

    public function findOrFail($id, array $columns = ['*'])
    {
        //reset model
        $this->getModel();

        return $this->model->findOrFail($id, $columns);
    }
}
