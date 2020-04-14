<?php

namespace Luminary\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Luminary\Http\Requests\Destroy;
use Luminary\Http\Requests\Index;
use Luminary\Http\Requests\Store;
use Luminary\Http\Requests\Update;
use Luminary\Models\Repositories\RelationshipRepository as Repository;
use Laravel\Lumen\Routing\Controller as BaseController;

class RelationshipController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param \Luminary\Http\Requests\Index $request
     * @param int $id
     * @param $relationship
     * @return \Illuminate\Database\Eloquent\Collection|Model
     */
    public function index(Index $request, $id, $relationship)
    {
        return str_singular($relationship) === $relationship
            ? Repository::find($id, $relationship)
            : Repository::all($id, $relationship);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Luminary\Http\Requests\Store $request
     * @param int $id
     * @param $relationship
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Store $request, $id, $relationship)
    {
        Repository::create($id, $relationship, $request->relationships());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Luminary\Http\Requests\Update $request
     * @param int $id
     * @param $relationship
     * @return bool
     */
    public function update(Update $request, $id, $relationship)
    {
        Repository::update($id, $relationship, $request->relationships());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Luminary\Http\Requests\Destroy $request
     * @param int $id
     * @param $relationship
     * @return bool
     */
    public function destroy(Destroy $request, $id, $relationship)
    {
        Repository::delete($id, $relationship, $request->relationships());
    }
}
