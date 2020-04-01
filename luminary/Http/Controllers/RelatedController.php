<?php

namespace Luminary\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Luminary\Http\Requests\Destroy;
use Luminary\Http\Requests\Index;
use Luminary\Http\Requests\Show;
use Luminary\Http\Requests\Store;
use Luminary\Http\Requests\Update;
use Luminary\Models\Repositories\RelatedRepository as Repository;
use Laravel\Lumen\Routing\Controller as BaseController;

class RelatedController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param \Luminary\Http\Requests\Index $request
     * @param int $id
     * @param $related
     * @return \Illuminate\Database\Eloquent\Collection|Model
     */
    public function index(Index $request, $id, $related)
    {
        return str_singular($related) === $related
            ? Repository::find($id, $related)
            : Repository::all($id, $related);
    }

    /**
     * Display an individual item of the resource.
     *
     * @param \Luminary\Http\Requests\Show $request
     * @param int $id
     * @param $related
     * @param $relatedId
     * @return \Illuminate\Database\Eloquent\Collection|Model
     */
    public function show(Show $request, $id, $related, $relatedId)
    {
        return Repository::find($id, $related, $relatedId);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Luminary\Http\Requests\Store $request
     * @param int $id
     * @param $related
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Store $request, $id, $related)
    {
        return Repository::create($id, $related, $request->data(), $request->relationships());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Luminary\Http\Requests\Update $request
     * @param int $id
     * @param $related
     * @param null $relatedId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(Update $request, $id, $related, $relatedId = null)
    {
        return Repository::update($id, $related, $relatedId, $request->data());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Luminary\Http\Requests\Destroy $request
     * @param int $id
     * @param $related
     * @param null $relatedId
     */
    public function destroy(Destroy $request, $id, $related, $relatedId = null)
    {
        Repository::delete($id, $related, $relatedId);
    }
}
