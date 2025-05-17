<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAlumniRequest;
use App\Http\Resources\AlumniResource;
use App\Models\Alumni;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function getAll(Request $request)
    {
        $selected_fields = [
            'id',
            'nama',
            'tempat_kerja',
            'jabatan_kerja',
            'tempat_kuliah',
            'prodi_kuliah',
            'photo'
        ];

        $data = Alumni::query()
            ->when($request->query('search'), function (Builder $query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('tempat_kerja', 'like', "%{$search}%")
                        ->orWhere('tempat_kuliah', 'like', "%{$search}%");
                });
            })
            ->when($request->query('tahun_mulai'), function (Builder $query, $tahun_mulai) {
                $query->where('tahun_mulai', $tahun_mulai);
            })
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->cursorPaginate(10);

        return AlumniResource::collection($data);
    }

    public function getDetail(Alumni $alumni)
    {
        return AlumniResource::make($alumni);
    }

    public function create(CreateAlumniRequest $request) {}
}
