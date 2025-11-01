<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Main\Config\AppConst;
use App\Main\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    protected $service;

    public function __construct(
        UserService $service
    )
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        $page = (int)$request->page;
        $data = [
            'page' => !empty($page) ? abs($page) : 1,
            'limit' => !empty($request->limit) ? (int)$request->limit : AppConst::PAGE_LIMIT,
            'keyword' => $request->keyword ?? '',
            'search_fields' => $request['search_fields'] ?? '',
        ];
        return $this->service->getAll($data);
    }

    public function store(UserFormRequest $request)
    {
        $password = Hash::make($request['password']);
        $data = [
            'data' =>
                [
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => $password,
                    'phone' => $request['phone'],
                ],
        ];

        return $this->service->save($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service->getById($id);
    }

    public function update(Request $request, $id)
    {

        return response(
            [
                'status' => 400,
                'message' => 'Not found',
            ]
            ,
            0
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->service->delete($id);
    }
}
