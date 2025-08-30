<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseBuilder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\KepemilikanUsahaRequest;
use App\Http\Resources\KepemilikanUsahaResource;
use App\Models\KepemilikanUsaha;

class KepemilikanUsahaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = KepemilikanUsaha::query()->latest('id')->cursorPaginate();

        return ResponseBuilder::success()
            ->data(KepemilikanUsahaResource::collection($data))
            ->pagination($data->nextCursor()?->encode(), $data->previousCursor()?->encode())
            ->build();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KepemilikanUsahaRequest $request)
    {
        $data = KepemilikanUsaha::create($request->validated());

        return ResponseBuilder::success()
            ->data(KepemilikanUsahaResource::make($data))
            ->message('Sukses menambahkan data baru')
            ->build();
    }

    /**
     * Display the specified resource.
     */
    public function show(KepemilikanUsaha $kepemilikanUsaha)
    {
        return ResponseBuilder::success()
            ->data(KepemilikanUsahaResource::make($kepemilikanUsaha))
            ->build();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KepemilikanUsahaRequest $request, KepemilikanUsaha $kepemilikanUsaha)
    {
        $kepemilikanUsaha->update($request->validated());

        return ResponseBuilder::success()
            ->data($kepemilikanUsaha)
            ->message('Sukses memperbarui data')
            ->build();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KepemilikanUsaha $kepemilikanUsaha)
    {
        $kepemilikanUsaha->delete();

        return ResponseBuilder::success()
            ->message('Sukses menghapus data')
            ->build();
    }
}
