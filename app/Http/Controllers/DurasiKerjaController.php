<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\DurasiKerjaRequest;
use App\Http\Resources\DurasiKerjaResource;
use App\Models\DurasiKerja;
use Illuminate\Http\Request;

class DurasiKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DurasiKerja::query()->latest('id')->cursorPaginate();

        return ResponseBuilder::success()
            ->data(DurasiKerjaResource::collection($data))
            ->pagination($data->nextCursor()?->encode(), $data->previousCursor()?->encode())
            ->build();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DurasiKerjaRequest $request)
    {
        $data = DurasiKerja::create($request->validated());

        return ResponseBuilder::success()
            ->data(DurasiKerjaResource::make($data))
            ->message('Sukses Membuat Data Baru')
            ->build();
    }

    /**
     * Display the specified resource.
     */
    public function show(DurasiKerja $durasiKerja)
    {
        return ResponseBuilder::success()
            ->data(DurasiKerjaResource::make($durasiKerja))
            ->build();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DurasiKerjaRequest $request, DurasiKerja $durasiKerja)
    {
        $durasiKerja->update($request->validated());

        return ResponseBuilder::success()
            ->data(DurasiKerjaResource::make($durasiKerja))
            ->build();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DurasiKerja $durasiKerja)
    {
        $durasiKerja->delete();

        return ResponseBuilder::success()
            ->message('Sukses Menghapus Data')
            ->build();
    }
}
