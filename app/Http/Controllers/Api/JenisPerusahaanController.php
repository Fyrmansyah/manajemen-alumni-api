<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseBuilder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JenisPerusahaanRequest;
use App\Http\Resources\JenisPerusahaanResource;
use App\Models\JenisPerusahaan;
use Illuminate\Http\Request;

class JenisPerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = JenisPerusahaan::query()->latest('id')->cursorPaginate();

        return ResponseBuilder::success()
            ->data(JenisPerusahaanResource::collection($data))
            ->pagination($data->nextCursor()?->encode(), $data->previousCursor()?->encode())
            ->build();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JenisPerusahaanRequest $request)
    {
        $data = JenisPerusahaan::create($request->validated());

        return ResponseBuilder::success()
            ->data($data)
            ->message('Sukses Menambahkan Data Baru')
            ->build();
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisPerusahaan $jenisPerusahaan)
    {
        return ResponseBuilder::success()
            ->data($jenisPerusahaan)
            ->build();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JenisPerusahaanRequest $request, JenisPerusahaan $jenisPerusahaan)
    {
        $jenisPerusahaan->update($request->validated());

        return ResponseBuilder::success()
            ->data($jenisPerusahaan)
            ->message('Sukses Memperbarui Data')
            ->build();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisPerusahaan $jenisPerusahaan)
    {
        $jenisPerusahaan->delete();

        return ResponseBuilder::success()
            ->message('Sukses Menghapus Data')
            ->build();
    }
}
