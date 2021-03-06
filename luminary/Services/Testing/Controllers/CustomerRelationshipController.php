<?php

namespace Luminary\Services\Testing\Controllers;

use Luminary\Services\ApiRequest\ApiRequest as Request;
use Luminary\Http\Controllers\Controller;
use Luminary\Services\Testing\Repositories\CustomerRelationshipRepository as Repository;

class CustomerRelationshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $id
     * @param $relationship
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index($id, $relationship)
    {
        return Repository::all($id, $relationship);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $id
     * @param $related
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Request $request, $id, $related)
    {
        Repository::create($id, $related, $request->relationships());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int $id
     * @param $related
     * @return bool
     */
    public function update(Request $request, $id, $related)
    {
        Repository::update($id, $related, $request->relationships());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @param $related
     * @return bool
     */
    public function destroy(Request $request, $id, $related)
    {
        Repository::delete($id, $related, $request->relationships());
    }
}
