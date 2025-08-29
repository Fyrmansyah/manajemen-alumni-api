<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\RangeGajiRequest;
use App\Http\Resources\RangeGajiResource;
use App\Models\RangeGaji;
use Illuminate\Http\Request;

class RangeGajiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = RangeGaji::query()->latest('id')->cursorPaginate();

        return ResponseBuilder::success()
            ->data(RangeGajiResource::collection($data))
            ->pagination($data->nextCursor()?->encode(), $data->previousCursor()?->encode())
            ->build();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RangeGajiRequest $request)
    {
        $data = RangeGaji::create($request->validated());

        return ResponseBuilder::success()
            ->message('Sukses menambahkan data baru')
            ->data(RangeGajiResource::make($data))
            ->build();
    }

    /**
     * Display the specified resource.
     */
    public function show(RangeGaji $rangeGaji)
    {
        return ResponseBuilder::success()
            ->data(RangeGajiResource::make($rangeGaji))
            ->build();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RangeGajiRequest $request, RangeGaji $rangeGaji)
    {
        $rangeGaji->update($request->validated());

        return ResponseBuilder::success()
            ->message('Sukses memperbarui data')
            ->data(RangeGajiResource::make($rangeGaji))
            ->build();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RangeGaji $rangeGaji)
    {
        $rangeGaji->delete();

        return ResponseBuilder::success()
            ->message('Sukses menghapus data')
            ->build();
    }
}
