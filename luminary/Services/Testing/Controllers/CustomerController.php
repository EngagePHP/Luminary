<?php

namespace Luminary\Services\Testing\Controllers;

use Luminary\Services\ApiRequest\ApiRequest as Request;
use Luminary\Http\Controllers\Controller;
use Luminary\Services\Testing\Repositories\CustomerRepository as Repository;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Repository::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Request $request)
    {
        return Repository::create($request->data(), $request->relationships());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function show($id)
    {
        return is_array($id)
            ? Repository::findAll($id)
            : Repository::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(Request $request, $id)
    {
        return Repository::update($id, $request->data(), $request->relationships());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy($id)
    {
        Repository::delete($id);
    }
}
