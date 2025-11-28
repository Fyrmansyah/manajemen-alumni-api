<?php

namespace App\Http\Controllers\Api;

use App\Exports\AlumnisExport;
use App\Helpers\ResponseBuilder;
use App\Http\Controllers\Controller;
use App\Imports\NisnImport;
use App\Models\Nisn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function importNisn()
    {
        try {
            Nisn::query()->doesntHave('alumni')->delete();
            Excel::import(new NisnImport, request()->file('alumni_excel'));

            return ResponseBuilder::success()
                ->message('Sukses import excel')
                ->build();
        } catch (\Throwable $th) {
            return ResponseBuilder::fail()
                ->message($th->getMessage())
                ->build();
        }
    }

    public  function exportAlumni(Request $request)
    {
        $fileName = 'data_alumni.xlsx';
        $path = 'exports/' . $fileName;

        $filters = [
            'kerja'   => $request->boolean('export_kerja'),
            'kuliah'  => $request->boolean('export_kuliah'),
            'usaha'   => $request->boolean('export_usaha'),
            'jobless' => $request->boolean('export_jobless'),
        ];

        if (!array_filter($filters)) {
            $filters = [];
        }

        Excel::store(new AlumnisExport($filters), $path);

        return response()->json([
            'fileUrl' => asset('storage/exports/' . $fileName),
            'fileName' => $fileName,
        ]);
    }
}
