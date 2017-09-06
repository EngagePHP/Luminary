<?php

namespace Luminary\Services\Testing\Controllers;

use Luminary\Services\ApiRequest\ApiRequest as Request;
use Luminary\Http\Controllers\Controller;
use Luminary\Services\Testing\Repositories\CustomerRelatedRepository as Repository;

class CustomerRelatedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $id
     * @param $related
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index($id, $related)
    {
        return Repository::all($id, $related);
    }

    /**
     * Display an individual item of the resource.
     *
     * @param $id
     * @param $related
     * @param $relatedId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function show($id, $related, $relatedId)
    {
        return Repository::find($id, $related, $relatedId);
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
        return Repository::create($id, $related, $request->data(), $request->relationships());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int $id
     * @param $related
     * @param null $relatedId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(Request $request, $id, $related, $relatedId = null)
    {
        return Repository::update($id, $related, $relatedId, $request->data());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param $related
     * @param null $relatedId
     */
    public function destroy($id, $related, $relatedId = null)
    {
        Repository::delete($id, $related, $relatedId);
    }
}
