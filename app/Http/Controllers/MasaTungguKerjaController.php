<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\MasaTungguKerjaRequest;
use App\Http\Resources\MasaTungguKerjaResource;
use App\Models\MasaTungguKerja;
use Illuminate\Http\Request;

class MasaTungguKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = MasaTungguKerja::query()->cursorPaginate();

        return ResponseBuilder::success()
            ->data(MasaTungguKerjaResource::collection($data))
            ->pagination($data->nextPageUrl(), $data->previousPageUrl())
            ->build();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MasaTungguKerjaRequest $request)
    {
        $data = MasaTungguKerja::create($request->validated());

        return ResponseBuilder::success()
            ->data($data)
            ->message('Sukses Menambahkan Data Baru')
            ->build();
    }

    /**
     * Display the specified resource.
     */
    public function show(MasaTungguKerja $masaTungguKerja)
    {
        return ResponseBuilder::success()
            ->data(MasaTungguKerjaResource::make($masaTungguKerja))
            ->build();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MasaTungguKerjaRequest $request, MasaTungguKerja $masaTungguKerja)
    {
        $data = $masaTungguKerja->update($request->validated());

        return ResponseBuilder::success()
            ->data(MasaTungguKerjaResource::make($masaTungguKerja))
            ->message('Sukses Memperbarui Data')
            ->build();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasaTungguKerja $masaTungguKerja)
    {
        $masaTungguKerja->delete();

        return ResponseBuilder::success()
            ->message('Berhasil Menghapus Data')
            ->build();
    }
}
