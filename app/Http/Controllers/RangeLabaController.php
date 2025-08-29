<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\RangeLabaRequest;
use App\Http\Resources\RangeLabaResource;
use App\Models\RangeLaba;
use Illuminate\Http\Request;

class RangeLabaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = RangeLaba::query()->latest('id')->cursorPaginate();

        return ResponseBuilder::success()
            ->data(RangeLabaResource::collection($data))
            ->pagination($data->nextCursor()?->encode(), $data->previousCursor()?->encode())
            ->build();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RangeLabaRequest $request)
    {
        $data = RangeLaba::create($request->validated());

        return ResponseBuilder::success()
            ->data(RangeLabaResource::make($data))
            ->message('Sukses menambah data baru')
            ->build();
    }

    /**
     * Display the specified resource.
     */
    public function show(RangeLaba $rangeLaba)
    {
        return ResponseBuilder::success()
            ->data($rangeLaba)
            ->build();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RangeLabaRequest $request, RangeLaba $rangeLaba)
    {
        $rangeLaba->update($request->validated());

        return ResponseBuilder::success()
            ->data(RangeLabaResource::make($rangeLaba))
            ->message('Sukses memperbarui data')
            ->build();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RangeLaba $rangeLaba)
    {
        $rangeLaba->delete();

        return ResponseBuilder::success()
            ->message('Sukses menghapus data')
            ->build();
    }
}
