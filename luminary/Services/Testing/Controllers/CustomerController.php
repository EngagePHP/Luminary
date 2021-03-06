<?php

namespace Luminary\Services\Testing\Controllers;

use Luminary\Http\Requests\Destroy;
use Luminary\Http\Requests\Index;
use Luminary\Http\Requests\Show;
use Luminary\Http\Requests\Store;
use Luminary\Http\Requests\Update;
use Luminary\Http\Controllers\Controller;
use Luminary\Services\Testing\Repositories\CustomerRepository as Repository;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Index $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index(Index $request)
    {
        return Repository::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Store $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Store $request)
    {
        return Repository::create($request->data(), $request->relationships());
    }

    /**
     * Display the specified resource.
     *
     * @param Show $request
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function show(Show $request, $id)
    {
        return is_array($id)
            ? Repository::findAll($id)
            : Repository::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update $request
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(Update $request, $id)
    {
        return Repository::update($id, $request->data(), $request->relationships());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Destroy $request
     * @param int $id
     */
    public function destroy(Destroy $request, $id)
    {
        Repository::delete($id);
    }
}
