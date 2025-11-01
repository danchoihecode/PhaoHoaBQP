<?php

namespace App\Main\Repositories;

use App\Main\BaseResponse\BaseRepository;
use App\Models\User;

/**
 *
 */
class UserRepository extends BaseRepository
{
    public function getModel()
    {
        return User::class;
    }

    public function getAll(array $params = [], $orderBy = 'id')
    {
        $query = User::query();
        //search
        $keyword = $params['keyword'] == null ? null : explode(',', $params['keyword']);
        $searchFields = $params['search_fields'] == null ? null : explode(',', $params['search_fields']);

        if (!empty($keyword) && !empty($searchFields)) {
            for ($i = 0; $i < count($searchFields); $i++)

                if ($searchFields[$i] == 'name' || $searchFields[$i] == 'status' ||   $searchFields[$i] == 'email' ) {
                    $stringLike = '%' . $keyword[$i] . '%';
                    $query->where($searchFields[$i], 'like', $stringLike);
                } else {
                    return [
                        'message' => 'search field not found',
                        'total' => 0,
                        'data' => [],
                    ];
                }
        }
        //pagination
        $page = $params['page'] ?? null;
        $limit = $params['limit'] ?? null;
        if (!empty($params['key_word']) && is_string($params['key_word'])) {
            $stringLike = '%' . $params['key_word'] . '%';
            $query->where('name', 'like', $stringLike);

        }
        $total = $query->count();
        if (!empty($limit) && !empty($page)) {
            $offset = ($page - 1) * $limit;
            $query->limit($limit)->offset($offset);
        }
        $query->get();
        $data = $query
            ->orderBy($orderBy)
            ->get();
        return [
            'data' => $data,
            'total' => $total,
            'message' => 'success',
        ];
    }

    public function getById($id)
    {
        $data = User::query()
            ->find($id);
        return $data;
    }

    public function getEmail()
    {
        $data = User::query()
            ->select('email')
            ->get();
        return $data;
    }

    public function create($data = [])
    {
        return parent::create($data);
    }

    public function has(string $name)
    {
        $this->has($name);
    }

    public function get(string $name)
    {
        $this->get($name);
    }

    public function set(string $name, string $value)
    {
        $this->set($name, $value);
    }

    public function clear(string $name)
    {
        $this->clear($name);
    }
}
