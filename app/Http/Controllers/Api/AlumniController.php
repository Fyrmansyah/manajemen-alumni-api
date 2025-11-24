<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseBuilder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateAlumniRequest;
use App\Http\Resources\Api\AlumniResource;
use App\Imports\AlumnisImport;
use App\Models\Alumni;
use App\Models\Nisn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AlumniController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        $includedRelations = ['jurusan', 'kuliahs', 'kerjas', 'usahas', 'nisn'];

        $data = Alumni::query()
            ->with($includedRelations)
            ->cursorPaginate();

        return ResponseBuilder::success()
            ->data(AlumniResource::collection($data))
            ->pagination($data->nextCursor()?->encode(), $data->previousCursor()?->encode())
            ->build();
    }

    public function getDetail(Request $request): JsonResponse
    {
        $includedRelations = ['jurusan', 'kuliahs', 'kerjas', 'usahas', 'nisn'];

        $alumni = Alumni::with($includedRelations)->find($request->alumni_id);
        if (!$alumni) {
            return ResponseBuilder::fail()
                ->message('Alumni dengan id: ' . $request->alumni_id . ' tidak ada')
                ->build();
        }

        return ResponseBuilder::success()
            ->data(AlumniResource::make($alumni))
            ->build();
    }

    public function checkEmailExist(Request $request): JsonResponse
    {
        $emailExist = Alumni::query()->where('email', $request->input('email'))->first();
        if ($emailExist) {
            return response()->json(true);
        }
        return response()->json(false);
    }

    public function create(CreateAlumniRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['tgl_lahir'] = date('Y-m-d', strtotime($data['tgl_lahir']));

        $nisn = Nisn::query()->where('number', $data['nisn'])->first('id');

        $alumni = Alumni::create([...$data, 'nisn_id' => $nisn->id]);

        if (!empty($data['kuliahs'])) {
            foreach ($data['kuliahs'] as $item) {
                $alumni->kuliahs()->create($item);
            }
        }

        if (!empty($data['kerjas'])) {
            foreach ($data['kerjas'] as $item) {
                $item['tgl_mulai'] = date('Y-m-d', strtotime($item['tgl_mulai']));
                $item['tgl_selesai'] = date('Y-m-d', strtotime($item['tgl_selesai']));
                $alumni->kerjas()->create($item);
            }
        }

        if (!empty($data['usahas'])) {
            foreach ($data['usahas'] as $item) {
                $item['tgl_mulai'] = date('Y-m-d', strtotime($item['tgl_mulai']));
                $item['tgl_selesai'] = date('Y-m-d', strtotime($item['tgl_selesai']));
                $alumni->usahas()->create($item);
            }
        }

        return ResponseBuilder::success()
            ->data($alumni)
            ->message('Sukses menambah data alumni baru')
            ->build();
    }

    public function update(CreateAlumniRequest $request, string $alumniId): JsonResponse
    {
        $alumni = Alumni::find($alumniId);
        if (!$alumni) {
            return ResponseBuilder::fail()
                ->message('data alumni tidak ditemukan')
                ->build();
        }

        $data = $request->validated();

        $data['tgl_lahir'] = date('Y-m-d', strtotime($data['tgl_lahir']));

        $nisn = Nisn::query()->where('number', $data['nisn'])->first('id');
        if ($nisn) {
            $data['nisn_id'] = $nisn->id;
        }

        $kuliahs = $data['kuliahs'] ?? [];
        $kerjas  = $data['kerjas'] ?? [];
        $usahas  = $data['usahas'] ?? [];

        unset($data['kuliahs'], $data['kerjas'], $data['usahas']);

        $alumni->update($data);

        $alumni->kuliahs()->delete();
        foreach ($kuliahs as $item) {
            $alumni->kuliahs()->create([
                'nama_kampus' => $item['nama_kampus'],
                'prodi' => $item['prodi'],
                'tahun_masuk' => $item['tahun_masuk'],
                'tahun_lulus' => $item['tahun_lulus'] ?? null,
                'sesuai_jurusan' => $item['sesuai_jurusan'],
                'jalur_masuk_kuliah_id' => $item['jalur_masuk_kuliah_id'],
            ]);
        }

        $alumni->kerjas()->delete();
        foreach ($kerjas as $item) {
            $alumni->kerjas()->create([
                'nama_perusahaan' => $item['nama_perusahaan'],
                'alamat_perusahaan' => $item['alamat_perusahaan'],
                'tgl_mulai' => date('Y-m-d', strtotime($item['tgl_mulai'])),
                'tgl_selesai' => $item['tgl_selesai']
                    ? date('Y-m-d', strtotime($item['tgl_selesai']))
                    : null,
                'sesuai_jurusan' => $item['sesuai_jurusan'],
                'jabatan' => $item['jabatan'],
                'masa_tunggu_kerja_id' => $item['masa_tunggu_kerja_id'],
                'jenis_perusahaan_id' => $item['jenis_perusahaan_id'],
                'durasi_kerja_id' => $item['durasi_kerja_id'],
                'range_gaji_id' => $item['range_gaji_id'],
            ]);
        }

        $alumni->usahas()->delete();
        foreach ($usahas as $item) {
            $alumni->usahas()->create([
                'nama_perusahaan' => $item['nama_perusahaan'],
                'bidang' => $item['bidang'],
                'jml_karyawan' => $item['jml_karyawan'] ?? null,
                'tgl_mulai' => date('Y-m-d', strtotime($item['tgl_mulai'])),
                'tgl_selesai' => $item['tgl_selesai']
                    ? date('Y-m-d', strtotime($item['tgl_selesai']))
                    : null,
                'sesuai_jurusan' => $item['sesuai_jurusan'],
                'kepemilikan_usaha_id' => $item['kepemilikan_usaha_id'],
                'range_laba_id' => $item['range_laba_id'],
            ]);
        }

        return ResponseBuilder::success()
            ->message('sukses memperbarui data alumni')
            ->data($alumni)
            ->build();
    }

    public function destroy(Request $request): JsonResponse
    {
        $alumni = Alumni::find($request->alumni_id);
        if (!$alumni) {
            return ResponseBuilder::fail()
                ->message('data alumni tidak ditemukan')
                ->build();
        }

        $alumni->delete();

        return ResponseBuilder::success()
            ->message('data alumni berhasil dihapus')
            ->build();
    }

    public function importExcel()
    {
        try {
            Excel::import(new AlumnisImport, request()->file('alumni_excel'));

            return ResponseBuilder::success()
                ->message('Sukses import excel')
                ->build();
        } catch (\Throwable $th) {
            return ResponseBuilder::fail()
                ->message($th->getMessage())
                ->build();
        }
    }

    public function getChart(Request $request): JsonResponse
    {
        $total_pengangguran = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereDoesntHave('kuliahs')
            ->whereDoesntHave('kerjas')
            ->whereDoesntHave('usahas')
            ->count();

        $total_kuliah = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereHas('kuliahs')
            ->whereDoesntHave('kerjas')
            ->whereDoesntHave('usahas')
            ->count();

        $total_kerja = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereHas('kerjas')
            ->whereDoesntHave('kuliahs')
            ->whereDoesntHave('usahas')
            ->count();

        $total_usaha = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereHas('usahas')
            ->whereDoesntHave('kuliahs')
            ->whereDoesntHave('kerjas')
            ->count();

        $total_kuliah_dan_kerja =  Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereHas('kuliahs')
            ->whereHas('kerjas')
            ->whereDoesntHave('usahas')
            ->count();

        $total_kuliah_dan_usaha =  Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereHas('kuliahs')
            ->whereHas('usahas')
            ->whereDoesntHave('kerjas')
            ->count();

        $total_kerja_dan_usaha =  Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereHas('kerjas')
            ->whereHas('usahas')
            ->whereDoesntHave('kuliahs')
            ->count();

        $total_kuliah_dan_kerja_dan_usaha =  Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereHas('kerjas')
            ->whereHas('usahas')
            ->whereHas('kuliahs')
            ->count();

        $bar_data = compact('total_pengangguran', 'total_kuliah', 'total_kerja', 'total_usaha', 'total_kuliah_dan_kerja', 'total_kuliah_dan_usaha', 'total_kerja_dan_usaha', 'total_kuliah_dan_kerja_dan_usaha');

        $pct_tidak_sesuai = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->where(function ($q) {
                $q->whereHas('kuliahs', fn($q2) => $q2->where('sesuai_jurusan', false))
                    ->orWhereHas('kerjas', fn($q2) => $q2->where('sesuai_jurusan', false))
                    ->orWhereHas('usahas', fn($q2) => $q2->where('sesuai_jurusan', false));
            })
            ->count();

        $pct_kuliah_sesuai = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereHas('kuliahs', fn($q) => $q->where('sesuai_jurusan', true))
            ->count();

        $pct_kerja_sesuai = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereHas('kerjas', fn($q) => $q->where('sesuai_jurusan', true))
            ->count();

        $pct_usaha_sesuai = Alumni::query()
            ->when($request->query('tahun_lulus'), function (Builder $query, $tahun_lulus) {
                $query->where('tahun_lulus', $tahun_lulus);
            })
            ->whereHas('usahas', fn($q) => $q->where('sesuai_jurusan', true))
            ->count();

        $total_pct = ($pct_tidak_sesuai + $pct_kuliah_sesuai + $pct_kerja_sesuai + $pct_usaha_sesuai);
        $pct_tidak_sesuai = round($pct_tidak_sesuai / $total_pct * 100);
        $pct_kuliah_sesuai = round($pct_kuliah_sesuai / $total_pct * 100);
        $pct_kerja_sesuai = round($pct_kerja_sesuai / $total_pct * 100);
        $pct_usaha_sesuai = round($pct_usaha_sesuai / $total_pct * 100);

        $pie_data = compact('pct_tidak_sesuai', 'pct_kuliah_sesuai', 'pct_kerja_sesuai', 'pct_usaha_sesuai');

        return ResponseBuilder::success()
            ->data(compact('bar_data', 'pie_data'))
            ->build();
    }

    public function checkNisnValid(string $nisn): JsonResponse
    {
        $valid_nisn = Nisn::query()->where('number', $nisn)->first();
        if (!$valid_nisn) {
            return ResponseBuilder::fail()->message('NISN Tidak ditemukan')->build();
        }

        return ResponseBuilder::success()
            ->message('NISN valid')
            ->data($valid_nisn)
            ->build();
    }
}
