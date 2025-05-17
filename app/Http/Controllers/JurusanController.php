<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJurusanRequest;
use App\Http\Requests\UpdateJurusanRequest;
use App\Http\Resources\JurusanResource;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function getAllJurusans(Request $request)
    {
        $data = Jurusan::query()
            ->where('nama', 'like', "%{$request->query('search')}%")
            ->paginate(10);

        return JurusanResource::collection($data);
    }

    public function createJurusan(CreateJurusanRequest $request)
    {
        Jurusan::create($request->validated());
    }

    public function update(UpdateJurusanRequest $request, Jurusan $jurusan)
    {
        $jurusan->update($request->validated());
    }

    public function deleteJurusan(Jurusan $jurusan)
    {
        $jurusan->delete();
    }
}
