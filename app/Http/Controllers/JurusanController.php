<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\CreateJurusanRequest;
use App\Http\Requests\UpdateJurusanRequest;
use App\Http\Resources\JurusanResource;
use App\Models\Jurusan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function getAllJurusans(Request $request): JsonResponse
    {
        $data = Jurusan::query()
            ->where('nama', 'like', "%{$request->query('search')}%")
            ->cursorPaginate(10);

        return ResponseBuilder::success()
            ->data(JurusanResource::collection($data))
            ->pagination($data->nextPageUrl(), $data->previousPageUrl())
            ->build();
    }

    public function createJurusan(CreateJurusanRequest $request): JsonResponse
    {
        $jurusan = Jurusan::create($request->validated());

        return ResponseBuilder::success()
            ->data($jurusan)
            ->build();
    }

    public function updateJurusan(UpdateJurusanRequest $request, string $jurusan_id): JsonResponse
    {
        $jurusan = Jurusan::find($jurusan_id);
        if (!$jurusan) {
            return ResponseBuilder::fail()
                ->message('data jurusan tidak ditemukan')
                ->build();
        }

        $jurusan = $jurusan->update($request->validated());

        return ResponseBuilder::success()
            ->data($jurusan)
            ->message('Sukses memperbarui data jurusan')
            ->build();
    }

    public function deleteJurusan(string $jurusan_id)
    {
        $jurusan = Jurusan::find($jurusan_id);
        if (!$jurusan) {
            return ResponseBuilder::fail()
                ->message('data jurusan tidak ditemukan')
                ->build();
        }

        $jurusan->delete();

        return ResponseBuilder::success()
            ->message('Sukses menghapus jurusan')
            ->build();
    }
}
