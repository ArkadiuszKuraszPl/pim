<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Producer;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\ResponsibleEntity;
use Illuminate\Http\RedirectResponse;

class ProducerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view("producers.index", [
            'producers' => Producer::all(),
            'countries' => Country::all(),
            'responsibleEntities' => ResponsibleEntity::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view("producers.create", [
            'countries' => Country::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $producer = new Producer($request->all());
        $producer->save();

        return redirect(route('producers.index'))->with('status', 'Dodano producenta');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producer $producer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producer $producer): View
    {
        return view("producers.edit", [
            'producer' => $producer,
            'countries' => Country::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producer $producer)
    {
        $producer->fill($request->all());
        $producer->save();

        return redirect(route('producers.index'))->with('status', 'Zaktualizowano producenta - ' . $producer->name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producer $producer)
    {
        //
    }
}
