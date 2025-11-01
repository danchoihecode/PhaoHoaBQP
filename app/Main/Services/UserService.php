<?php

namespace App\Main\Services;

use App\Main\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Throwable;
use function PHPUnit\Framework\isEmpty;
use function App\Main\Helpers\responseJsonFail;
use function App\Main\Helpers\responseJsonSuccess;
use function App\Main\Helpers\responseJsonSuccessPaginate;
use function App\Main\Helpers\paginate;


class UserService
{
    protected UserRepository $repository;

    public function __construct(
        UserRepository $repository
    )
    {
        $this->repository = $repository;

    }

    public function getEmail()
    {
        return $this->repository->getEmail();
    }

    public function getAll($data)
    {
        $result = $this->repository->getAll($data);
        $message = $result['message'];
        if (isEmpty($message) && $message != 'success') {
            return responseJsonFail($message);
        }
        $total = $result['total'];
        $limit = $data['limit'];
        $page = $data['page'];
        $paginate = paginate($total, $limit, $page);
        return responseJsonSuccessPaginate($result['data'], $paginate);
    }

    public function getById($id)
    {
        $data = $this->repository->getById($id);
        if ($data) {
            return responseJsonSuccess($data);
        }
        return responseJsonFail(false);
    }


    public function save($data)
    {
        DB::beginTransaction();
        try {

            if (empty($data['id'])) {

                $result = $this->createData($data);
            } else {
                $result = $this->updateData($data);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            error_log($e->getMessage());
            if ($e->getCode() == 23000) {
                return responseJsonFail("The email has already been taken. ");
            }
            return responseJsonFail(false);
        }

        return responseJsonSuccess($result);
    }

    private function createData($data)
    {

        return $this->repository->create($data['data']);
    }

    private function updateData($data)
    {
        $user = $this->repository->findOne('id', $data['id']);
        if (empty($user)) {
            return responseJsonFail(false);
        }
        foreach ($data['data'] as $key => $value) {

            if ($key != 'status') {
                $user->$key = $value;
            }
        }
        $status = $data['data']['status'];
        if (!isEmpty($status)) {
            $user->status = $data['data']['status'];
        }
        $user->save();
        return $user;
    }

    public function delete($id)
    {
        return $this->save(
            [
                'id' => $id,
                'data' => [
                    'status' => 2
                ]
            ]
        );
    }

    public function deleteUser($id)
    {
        $result = $this->repository->delete($id);
        return responseJsonSuccess($result);

    }
}
