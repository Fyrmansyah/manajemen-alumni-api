<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\JalurMasukKuliahRequest;
use App\Http\Resources\JalurMasukKuliahResource;
use App\Models\JalurMasukKuliah;

class JalurMasukKuliahController extends Controller
{
    public function index()
    {
        $data = JalurMasukKuliah::query()->cursorPaginate();

        return ResponseBuilder::success()
            ->data(JalurMasukKuliahResource::collection($data))
            ->pagination($data->nextPageUrl(), $data->previousPageUrl())
            ->build();
    }

    public function store(JalurMasukKuliahRequest $request)
    {
        $data = JalurMasukKuliah::create($request->validated());

        return ResponseBuilder::success()
            ->data($data)
            ->message('Sukses Menambahkan Jalur Masuk Baru')
            ->build();
    }

    public function show(JalurMasukKuliah $jalurMasukKuliah)
    {
        return ResponseBuilder::success()
            ->data($jalurMasukKuliah)
            ->build();
    }

    public function update(JalurMasukKuliahRequest $request, JalurMasukKuliah $jalurMasukKuliah)
    {
        $data = $jalurMasukKuliah->update($request->validated());

        return ResponseBuilder::success()
            ->data($data)
            ->message('Sukses Update Data')
            ->build();
    }

    public function destroy(JalurMasukKuliah $jalurMasukKuliah)
    {
        $jalurMasukKuliah->delete();

        return ResponseBuilder::fail()
            ->message('Data Sukses Dihapus')
            ->build();
    }
}
