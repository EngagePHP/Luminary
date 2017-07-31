<?php

namespace Luminary\Services\Testing\Controllers;

use Luminary\Services\ApiRequest\ApiRequest as Request;
use Luminary\Http\Controllers\Controller;
use Luminary\Services\Testing\Repositories\CustomerRepository;

class CustomerController extends Controller
{
    public function index()
    {
        return CustomerRepository::all();
    }

    public function store(Request $request)
    {
        return CustomerRepository::create($request->all(), $request->getRelationships());
    }

    public function show($id)
    {
        return CustomerRepository::find($id);
    }

    public function update(Request $request, $id)
    {
        return CustomerRepository::update($id, $request->all(), $request->getRelationships());
    }

    public function destroy($id)
    {
        CustomerRepository::delete($id);
    }
}
