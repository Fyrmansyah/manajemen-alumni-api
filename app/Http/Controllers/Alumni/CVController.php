<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;
use Dompdf\Dompdf as BaseDompdf;
use Dompdf\Options;

class CVController extends Controller
{
    public function index()
    {
        $alumni = Auth::guard('alumni')->user();
        $cvs = $alumni->cvs ?? collect();
        
        return view('alumni.cv.index', compact('alumni', 'cvs'));
    }

    public function create()
    {
        $alumni = Auth::guard('alumni')->user();
        $templates = $this->getTemplates();
        
        return view('alumni.cv.create', compact('alumni', 'templates'));
    }

    public function store(Request $request)
    {
        try {
            // Base validation rules
            $rules = [
                'title' => 'required|string|max:255',
                'template' => 'required|string|in:modern,classic,creative',
                'data_source' => 'required|in:profile,custom',
                'set_as_default' => 'boolean',
            ];
            
            // Add custom field validation only if data_source is 'custom'
            if ($request->input('data_source') === 'custom') {
                $rules = array_merge($rules, [
                    'custom_name' => 'required|string|max:255',
                    'custom_last_name' => 'nullable|string|max:255',
                    'custom_email' => 'required|email',
                    'custom_phone' => 'nullable|string|max:20',
                    'custom_photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                    'custom_birth_date' => 'nullable|date',
                    'custom_birth_place' => 'nullable|string|max:255',
                    'custom_address' => 'nullable|string|max:500',
                    'custom_postal_code' => 'nullable|string|max:10',
                    'custom_city' => 'nullable|string|max:255',
                    'custom_gender' => 'nullable|string|in:Laki-laki,Perempuan',
                    'custom_nationality' => 'nullable|string|max:255',
                    'custom_skills' => 'nullable|string|max:2000',
                    
                    // Experience fields (arrays)
                    'custom_job_title' => 'nullable|array',
                    'custom_job_title.*' => 'nullable|string|max:255',
                    'custom_company' => 'nullable|array',
                    'custom_company.*' => 'nullable|string|max:255',
                    'custom_start_month' => 'nullable|array',
                    'custom_start_month.*' => 'nullable|string',
                    'custom_start_year' => 'nullable|array',
                    'custom_start_year.*' => 'nullable|string',
                    'custom_end_month' => 'nullable|array',
                    'custom_end_month.*' => 'nullable|string',
                    'custom_end_year' => 'nullable|array',
                    'custom_end_year.*' => 'nullable|string',
                    'custom_job_description' => 'nullable|array',
                    'custom_job_description.*' => 'nullable|string|max:1000',
                    
                    // Education fields (arrays)
                    'custom_school_name' => 'nullable|array',
                    'custom_school_name.*' => 'nullable|string|max:255',
                    'custom_degree' => 'nullable|array',
                    'custom_degree.*' => 'nullable|string|max:255',
                    'custom_edu_start_month' => 'nullable|array',
                    'custom_edu_start_month.*' => 'nullable|string',
                    'custom_edu_start_year' => 'nullable|array',
                    'custom_edu_start_year.*' => 'nullable|string',
                    'custom_edu_end_month' => 'nullable|array',
                    'custom_edu_end_month.*' => 'nullable|string',
                    'custom_edu_end_year' => 'nullable|array',
                    'custom_edu_end_year.*' => 'nullable|string',
                    'custom_edu_description' => 'nullable|array',
                    'custom_edu_description.*' => 'nullable|string|max:500',
                    
                    // Reference fields
                    'references_available' => 'nullable|boolean',
                    'custom_ref_name_1' => 'nullable|string|max:255',
                    'custom_ref_position_1' => 'nullable|string|max:255',
                    'custom_ref_phone_1' => 'nullable|string|max:20',
                    'custom_ref_name_2' => 'nullable|string|max:255',
                    'custom_ref_position_2' => 'nullable|string|max:255',
                    'custom_ref_phone_2' => 'nullable|string|max:20',
                ]);
            }
            
            $request->validate($rules);

            $alumni = Auth::guard('alumni')->user();
            
            // Load jurusan relationship to avoid N+1 queries
            $alumni->load('jurusan');
            
            // Generate CV data
            $cvData = $this->generateCVData($request, $alumni);
        
            // Generate PDF
            $pdf = $this->generatePDF($cvData, $request->template);
            
            // Save CV
            $filename = 'cv_' . time() . '_' . $alumni->id . '.pdf';
            Storage::disk('public')->put('cvs/' . $filename, $pdf);
            
            // Save CV record to database
            $cv = $alumni->cvs()->create([
                'title' => $request->title,
                'template' => $request->template,
                'filename' => $filename,
                'data' => $cvData,
                'is_default' => $request->has('set_as_default'),
            ]);
            
            // If this is set as default, unset others
            if ($request->has('set_as_default')) {
                $alumni->cvs()->where('id', '!=', $cv->id)->update(['is_default' => false]);
            }
            
            return redirect()->route('alumni.cv.index')->with('success', 'CV berhasil dibuat!');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat CV: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $alumni = Auth::guard('alumni')->user();
        $cv = $alumni->cvs()->findOrFail($id);
        
        return view('alumni.cv.show', compact('cv'));
    }

    public function download($id)
    {
        $alumni = Auth::guard('alumni')->user();
        $cv = $alumni->cvs()->findOrFail($id);
        
        $path = Storage::disk('public')->path('cvs/' . $cv->filename);
        
        return response()->download($path, $cv->title . '.pdf');
    }

    public function preview($id)
    {
        $alumni = Auth::guard('alumni')->user();
        
        if (!$alumni) {
            abort(401, 'Unauthorized - Alumni not authenticated');
        }
        
        $cv = $alumni->cvs()->findOrFail($id);
        
        $path = Storage::disk('public')->path('cvs/' . $cv->filename);
        
        // Periksa apakah file ada
        if (!file_exists($path)) {
            abort(404, 'File CV tidak ditemukan: ' . $path);
        }
        
        // Return PDF untuk preview di browser (bukan download)
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $cv->title . '.pdf"'
        ]);
    }

    public function destroy($id)
    {
        $alumni = Auth::guard('alumni')->user();
        $cv = $alumni->cvs()->findOrFail($id);
        
        // Delete file
        Storage::disk('public')->delete('cvs/' . $cv->filename);
        
        // Delete record
        $cv->delete();
        
        return redirect()->route('alumni.cv.index')->with('success', 'CV berhasil dihapus!');
    }

    public function setAsDefault($id)
    {
        $alumni = Auth::guard('alumni')->user();
        $cv = $alumni->cvs()->findOrFail($id);
        
        // Unset all other CVs as default
        $alumni->cvs()->update(['is_default' => false]);
        
        // Set this CV as default
        $cv->update(['is_default' => true]);
        
        return redirect()->route('alumni.cv.index')->with('success', 'CV berhasil diatur sebagai default!');
    }

    private function getTemplates()
    {
        return [
            'modern' => [
                'name' => 'Modern',
                'description' => 'Template modern dengan layout yang clean dan profesional',
                'preview' => 'modern-preview.png'
            ],
            'classic' => [
                'name' => 'Classic',
                'description' => 'Template klasik yang formal dan mudah dibaca',
                'preview' => 'classic-preview.png'
            ],
            'creative' => [
                'name' => 'Creative',
                'description' => 'Template kreatif dengan warna-warna yang dinamis',
                'preview' => 'creative-preview.png'
            ]
        ];
    }

    private function generateCVData(Request $request, $alumni)
    {
        if ($request->input('data_source') === 'profile') {
            // Use profile data with proper fallbacks and formatting
            // Access raw attributes to avoid accessor method issues
            $namaLengkap = $alumni->getAttributeValue('nama_lengkap') ?: $alumni->getAttributeValue('nama');
            $phone = $alumni->getAttributeValue('phone') ?: $alumni->getAttributeValue('no_tlp');
            $tanggalLahir = $alumni->getAttributeValue('tanggal_lahir') ?: $alumni->getAttributeValue('tgl_lahir');
            
            return [
                'name' => $namaLengkap ?: 'Nama Lengkap',
                'email' => $alumni->email ?: 'email@example.com',
                'phone' => $phone ?: 'No. Telepon',
                'address' => $alumni->alamat ?: 'Alamat',
                'birth_date' => $tanggalLahir,
                'birth_place' => $alumni->getAttributeValue('tempat_lahir') ?? 'Tempat Lahir',
                'gender' => $alumni->jenis_kelamin ? ($alumni->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan') : 'Jenis Kelamin',
                'nisn' => $alumni->nisn ?: 'NISN',
                'education' => [
                    'school' => 'SMKN 1 Surabaya',
                    'major' => $alumni->jurusan ? $alumni->jurusan->nama : 'Jurusan',
                    'start_year' => $alumni->tahun_mulai ?: 'Tahun Mulai',
                    'graduation_year' => $alumni->tahun_lulus ?: 'Tahun Lulus',
                    'nisn' => $alumni->nisn ?: 'NISN',
                ],
                'work_experience' => [
                    'current_job' => $alumni->tempat_kerja ?: null,
                    'current_position' => $alumni->jabatan_kerja ?: null,
                    'job_suitability' => $alumni->kesesuaian_kerja,
                    'experience_detail' => $alumni->pengalaman_kerja ?: 'Belum ada pengalaman kerja',
                ],
                'higher_education' => [
                    'university' => $alumni->tempat_kuliah ?: null,
                    'study_program' => $alumni->prodi_kuliah ?: null,
                    'education_suitability' => $alumni->kesesuaian_kuliah,
                ],
                'skills' => $alumni->keahlian ?: 'Belum ada keahlian yang dicantumkan',
                'photo' => $alumni->foto ? asset('storage/photos/' . $alumni->foto) : null,
                'created_at' => now()->format('d F Y'),
            ];
        } else {
            // Use custom data from form
            $fullName = trim($request->input('custom_name') . ' ' . $request->input('custom_last_name'));
            
            // Process experience data
            $experiences = [];
            $jobTitles = $request->input('custom_job_title', []);
            $companies = $request->input('custom_company', []);
            $startMonths = $request->input('custom_start_month', []);
            $startYears = $request->input('custom_start_year', []);
            $endMonths = $request->input('custom_end_month', []);
            $endYears = $request->input('custom_end_year', []);
            $jobDescriptions = $request->input('custom_job_description', []);
            
            for ($i = 0; $i < count($jobTitles); $i++) {
                if (!empty($jobTitles[$i]) || !empty($companies[$i])) {
                    $startDate = '';
                    if (!empty($startMonths[$i]) && !empty($startYears[$i])) {
                        $startDate = $this->getMonthName($startMonths[$i]) . ' ' . $startYears[$i];
                    }
                    
                    $endDate = '';
                    if (!empty($endMonths[$i])) {
                        if ($endMonths[$i] === 'current') {
                            $endDate = 'Sekarang';
                        } elseif (!empty($endYears[$i])) {
                            $endDate = $this->getMonthName($endMonths[$i]) . ' ' . $endYears[$i];
                        }
                    }
                    
                    $experiences[] = [
                        'job_title' => $jobTitles[$i] ?? '',
                        'company' => $companies[$i] ?? '',
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'description' => $jobDescriptions[$i] ?? '',
                    ];
                }
            }
            
            // Process education data
            $educations = [];
            $schoolNames = $request->input('custom_school_name', []);
            $degrees = $request->input('custom_degree', []);
            $eduStartMonths = $request->input('custom_edu_start_month', []);
            $eduStartYears = $request->input('custom_edu_start_year', []);
            $eduEndMonths = $request->input('custom_edu_end_month', []);
            $eduEndYears = $request->input('custom_edu_end_year', []);
            $eduDescriptions = $request->input('custom_edu_description', []);
            
            for ($i = 0; $i < count($schoolNames); $i++) {
                if (!empty($schoolNames[$i]) || !empty($degrees[$i])) {
                    $startDate = '';
                    if (!empty($eduStartMonths[$i]) && !empty($eduStartYears[$i])) {
                        $startDate = $this->getMonthName($eduStartMonths[$i]) . ' ' . $eduStartYears[$i];
                    }
                    
                    $endDate = '';
                    if (!empty($eduEndMonths[$i])) {
                        if ($eduEndMonths[$i] === 'current') {
                            $endDate = 'Sekarang';
                        } elseif (!empty($eduEndYears[$i])) {
                            $endDate = $this->getMonthName($eduEndMonths[$i]) . ' ' . $eduEndYears[$i];
                        }
                    }
                    
                    $educations[] = [
                        'school_name' => $schoolNames[$i] ?? '',
                        'degree' => $degrees[$i] ?? '',
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'description' => $eduDescriptions[$i] ?? '',
                    ];
                }
            }
            
            // Process references
            $references = [];
            if ($request->input('references_available')) {
                if ($request->input('custom_ref_name_1')) {
                    $references[] = [
                        'name' => $request->input('custom_ref_name_1'),
                        'position' => $request->input('custom_ref_position_1'),
                        'phone' => $request->input('custom_ref_phone_1'),
                    ];
                }
                if ($request->input('custom_ref_name_2')) {
                    $references[] = [
                        'name' => $request->input('custom_ref_name_2'),
                        'position' => $request->input('custom_ref_position_2'),
                        'phone' => $request->input('custom_ref_phone_2'),
                    ];
                }
            }
            
            return [
                'name' => $fullName ?: 'Nama Lengkap',
                'email' => $request->input('custom_email') ?: 'email@example.com',
                'phone' => $request->input('custom_phone') ?: 'No. Telepon',
                'address' => $request->input('custom_address') ?: 'Alamat',
                'city' => $request->input('custom_city') ?: '',
                'postal_code' => $request->input('custom_postal_code') ?: '',
                'birth_date' => $request->input('custom_birth_date'),
                'birth_place' => $request->input('custom_birth_place') ?: 'Tempat Lahir',
                'gender' => $request->input('custom_gender') ?: 'Jenis Kelamin',
                'nationality' => $request->input('custom_nationality') ?: 'Indonesia',
                'nisn' => $alumni->nisn ?: 'NISN',
                'experiences' => $experiences,
                'educations' => $educations,
                'skills' => $request->input('custom_skills') ?: 'Belum ada keahlian yang dicantumkan',
                'references' => $references,
                'references_available' => $request->input('references_available', false),
                'photo' => $this->getPhotoBase64($request, $alumni),
                'created_at' => now()->format('d F Y'),
                
                // Legacy fields for backward compatibility
                'education' => [
                    'school' => !empty($educations) ? $educations[0]['school_name'] : 'SMKN 1 Surabaya',
                    'major' => !empty($educations) ? $educations[0]['degree'] : ($alumni->jurusan ? $alumni->jurusan->nama : 'Jurusan'),
                    'start_year' => $alumni->tahun_mulai ?: 'Tahun Mulai',
                    'graduation_year' => $alumni->tahun_lulus ?: 'Tahun Lulus',
                    'nisn' => $alumni->nisn ?: 'NISN',
                ],
                'work_experience' => [
                    'current_job' => !empty($experiences) ? $experiences[0]['company'] : null,
                    'current_position' => !empty($experiences) ? $experiences[0]['job_title'] : null,
                    'job_suitability' => null,
                    'experience_detail' => !empty($experiences) ? $experiences[0]['description'] : 'Belum ada pengalaman kerja',
                ],
                'higher_education' => [
                    'university' => null,
                    'study_program' => null,
                    'education_suitability' => null,
                ],
            ];
        }
    }

    private function generatePDF($data, $template)
    {
        // Generate HTML based on template
        $html = $this->generateHTML($data, $template);
        
        try {
            // Method 1: Try Laravel DomPDF Facade
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->output();
        } catch (\Exception $e) {
            try {
                // Method 2: Try DomPDF wrapper from service container
                $pdf = app('dompdf.wrapper');
                $pdf->loadHTML($html);
                $pdf->setPaper('A4', 'portrait');
                
                return $pdf->output();
            } catch (\Exception $e2) {
                try {
                    // Method 3: Use direct Dompdf instantiation
                    $options = new Options();
                    $options->set('defaultFont', 'Arial');
                    $options->set('isRemoteEnabled', true);
                    
                    $dompdf = new BaseDompdf($options);
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    
                    return $dompdf->output();
                } catch (\Exception $e3) {
                    // If all methods fail, return error
                    throw new \Exception('PDF generation failed: ' . $e3->getMessage());
                }
            }
        }
    }

    private function generateHTML($data, $template)
    {
        // Use public_path for DomPDF compatibility
        // Main watermark
        $watermarkPath = public_path('assets/images/watermark-smkn1.png');
        $watermarkBase64 = '';
        if (file_exists($watermarkPath)) {
            $watermarkBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($watermarkPath));
        }
        // BKK logo watermark
        $bkkLogoPath = public_path('assets/images/logo BKK.png');
        $bkkLogoBase64 = '';
        if (file_exists($bkkLogoPath)) {
            $bkkLogoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bkkLogoPath));
        }
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>CV - ' . htmlspecialchars($data['name']) . '</title>
            <style>
                body {
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    margin: 0;
                    padding: 15px;
                    background: #fff;
                    position: relative;
                    color: #333;
                    line-height: 1.6;
                    font-size: 14px;
                }
                .watermark {
                    position: absolute;
                    top: 35%;
                    left: 50%;
                    width: 400px;
                    height: auto;
                    opacity: 0.08;
                    z-index: 0;
                    transform: translate(-50%, -50%);
                }
                .bkk-watermark {
                    position: absolute;
                    bottom: 40px;
                    right: 40px;
                    width: 120px;
                    height: auto;
                    opacity: 0.12;
                    z-index: 0;
                }
                .cv-container {
                    background: #fff;
                    border-radius: 10px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
                    max-width: 750px;
                    margin: 0 auto;
                    padding: 25px 35px;
                    position: relative;
                    z-index: 1;
                }
                .personal-info {
                    display: table;
                    width: 100%;
                    margin-bottom: 25px;
                    border-bottom: 3px solid #2563eb;
                    padding-bottom: 20px;
                }
                .photo-section {
                    display: table-cell;
                    width: 120px;
                    vertical-align: top;
                    padding-right: 25px;
                }
                .personal-photo {
                    width: 100px;
                    height: 120px;
                    border-radius: 8px;
                    background: #f0f0f0;
                    object-fit: cover;
                    border: 2px solid #e5e7eb;
                }
                .details-section {
                    display: table-cell;
                    vertical-align: top;
                }
                .details-section h1 {
                    font-size: 24px;
                    margin: 0 0 15px 0;
                    color: #2563eb;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .info-table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .info-table tr {
                    margin-bottom: 8px;
                }
                .info-table td:first-child {
                    width: 140px;
                    font-weight: 600;
                    color: #555;
                    padding: 3px 0;
                    vertical-align: top;
                }
                .info-table td:nth-child(2) {
                    width: 10px;
                    padding: 3px 0;
                    vertical-align: top;
                }
                .info-table td:last-child {
                    padding: 3px 0;
                    color: #333;
                    vertical-align: top;
                    word-wrap: break-word;
                }
                .section {
                    margin-bottom: 20px;
                    page-break-inside: avoid;
                }
                .section-title {
                    color: #2563eb;
                    font-size: 16px;
                    font-weight: 700;
                    margin-bottom: 12px;
                    border-left: 4px solid #2563eb;
                    padding-left: 12px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .section-content {
                    margin-left: 16px;
                }
                .entry-item {
                    margin-bottom: 15px;
                    padding: 12px;
                    background: #f8fafc;
                    border-left: 4px solid #3b82f6;
                    border-radius: 0 6px 6px 0;
                }
                .entry-title {
                    font-weight: 600;
                    color: #1e40af;
                    font-size: 15px;
                    margin-bottom: 4px;
                }
                .entry-subtitle {
                    font-style: italic;
                    color: #6b7280;
                    font-size: 14px;
                    margin-bottom: 6px;
                }
                .entry-date {
                    color: #9ca3af;
                    font-size: 12px;
                    margin-bottom: 8px;
                    font-weight: 500;
                }
                .entry-description {
                    color: #374151;
                    font-size: 13px;
                    line-height: 1.5;
                }
                .skills-list {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                    margin-top: 5px;
                }
                .skill-tag {
                    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
                    color: #1e40af;
                    border-radius: 15px;
                    padding: 6px 14px;
                    font-size: 12px;
                    font-weight: 500;
                    border: 1px solid #c7d2fe;
                }
                .footer {
                    margin-top: 25px;
                    text-align: center;
                    color: #9ca3af;
                    font-size: 11px;
                    border-top: 1px solid #e5e7eb;
                    padding-top: 15px;
                }
                @media print {
                    body { padding: 0; }
                    .cv-container { box-shadow: none; }
                }
            </style>
        </head>
        <body>
            <div class="watermark">
                '.($watermarkBase64 ? '<img src="'.$watermarkBase64.'" alt="SMKN 1 Surabaya" style="width: 400px; height: auto;">' : '').'
            </div>
            <div class="bkk-watermark">
                '.($bkkLogoBase64 ? '<img src="'.$bkkLogoBase64.'" alt="BKK SMKN 1 Surabaya" style="width: 120px; height: auto;">' : '').'
            </div>
            <div class="cv-container">
                <div class="personal-info">
                    <div class="photo-section">
                        <img class="personal-photo" src="'.(isset($data['photo']) && $data['photo'] ? htmlspecialchars($data['photo']) : 'https://ui-avatars.com/api/?name='.urlencode($data['name']).'&background=E0E7FF&color=1E40AF&size=120').'" alt="Foto Profil">
                    </div>
                    <div class="details-section">
                        <h1>' . htmlspecialchars($data['name']) . '</h1>
                        <table class="info-table">
                            <tr><td>Email</td><td>:</td><td>' . htmlspecialchars($data['email']) . '</td></tr>
                            <tr><td>Telepon</td><td>:</td><td>' . htmlspecialchars($data['phone']) . '</td></tr>
                            <tr><td>Alamat</td><td>:</td><td>' . htmlspecialchars($data['address'] ?? '-') . (isset($data['city']) && $data['city'] ? ', ' . htmlspecialchars($data['city']) : '') . (isset($data['postal_code']) && $data['postal_code'] ? ' ' . htmlspecialchars($data['postal_code']) : '') . '</td></tr>
                            <tr><td>Tempat, Tgl Lahir</td><td>:</td><td>' . htmlspecialchars(($data['birth_place'] ?? '-') . ', ' . ($data['birth_date'] ?? '-')) . '</td></tr>
                            <tr><td>Jenis Kelamin</td><td>:</td><td>' . htmlspecialchars($data['gender'] ?? '-') . '</td></tr>';
                            
        if (isset($data['nationality']) && $data['nationality']) {
            $html .= '<tr><td>Kewarganegaraan</td><td>:</td><td>' . htmlspecialchars($data['nationality']) . '</td></tr>';
        }
        
        $html .= '<tr><td>NISN</td><td>:</td><td>' . htmlspecialchars($data['nisn'] ?? '-') . '</td></tr>
                        </table>
                    </div>
                </div>';

        // Education Section - check if we have detailed education data
        $html .= '<div class="section">
                    <div class="section-title">Pendidikan</div>
                    <div class="section-content">';
        
        if (isset($data['educations']) && !empty($data['educations'])) {
            // Display detailed education data from form
            foreach ($data['educations'] as $education) {
                $html .= '<div class="entry-item">
                            <div class="entry-title">' . htmlspecialchars($education['school_name']) . '</div>
                            <div class="entry-subtitle">' . htmlspecialchars($education['degree']) . '</div>';
                
                if ($education['start_date'] || $education['end_date']) {
                    $html .= '<div class="entry-date">';
                    if ($education['start_date']) {
                        $html .= htmlspecialchars($education['start_date']);
                    }
                    if ($education['start_date'] && $education['end_date']) {
                        $html .= ' - ';
                    }
                    if ($education['end_date']) {
                        $html .= htmlspecialchars($education['end_date']);
                    }
                    $html .= '</div>';
                }
                
                if ($education['description']) {
                    $html .= '<div class="entry-description">' . nl2br(htmlspecialchars($education['description'])) . '</div>';
                }
                
                $html .= '</div>';
            }
        } else {
            // Display basic education data
            $html .= '<div class="entry-item">
                        <div class="entry-title">' . htmlspecialchars($data['education']['school']) . '</div>
                        <div class="entry-subtitle">Jurusan: ' . htmlspecialchars($data['education']['major']) . '</div>
                        <div class="entry-date">' . htmlspecialchars($data['education']['start_year']) . ' - ' . htmlspecialchars($data['education']['graduation_year']) . '</div>
                      </div>';
        }
        
        $html .= '</div>
                </div>';

        // Work Experience Section
        $html .= '<div class="section">
                    <div class="section-title">Pengalaman Kerja</div>
                    <div class="section-content">';
        
        if (isset($data['experiences']) && !empty($data['experiences'])) {
            // Display detailed experience data from form
            foreach ($data['experiences'] as $experience) {
                $html .= '<div class="entry-item">
                            <div class="entry-title">' . htmlspecialchars($experience['job_title']) . '</div>
                            <div class="entry-subtitle">' . htmlspecialchars($experience['company']) . '</div>';
                
                if ($experience['start_date'] || $experience['end_date']) {
                    $html .= '<div class="entry-date">';
                    if ($experience['start_date']) {
                        $html .= htmlspecialchars($experience['start_date']);
                    }
                    if ($experience['start_date'] && $experience['end_date']) {
                        $html .= ' - ';
                    }
                    if ($experience['end_date']) {
                        $html .= htmlspecialchars($experience['end_date']);
                    }
                    $html .= '</div>';
                }
                
                if ($experience['description']) {
                    $html .= '<div class="entry-description">' . nl2br(htmlspecialchars($experience['description'])) . '</div>';
                }
                
                $html .= '</div>';
            }
        } else {
            // Display basic experience data
            $html .= '<div class="entry-item">
                        <div class="entry-description">' . nl2br(htmlspecialchars($data['work_experience']['experience_detail'] ?? 'Belum ada pengalaman kerja')) . '</div>
                      </div>';
        }
        
        $html .= '</div>
                </div>';

        // Skills Section
        $html .= '<div class="section">
                    <div class="section-title">Keahlian</div>
                    <div class="section-content skills-list">';
        
        if (!empty($data['skills']) && $data['skills'] !== 'Belum ada keahlian yang dicantumkan') {
            $skills = is_array($data['skills']) ? $data['skills'] : array_map('trim', explode(',', $data['skills']));
            $skills = array_filter($skills); // Remove empty values
            
            if (!empty($skills)) {
                foreach ($skills as $skill) {
                    $html .= '<span class="skill-tag">' . htmlspecialchars($skill) . '</span>';
                }
            } else {
                $html .= '<span class="skill-tag">' . htmlspecialchars($data['skills']) . '</span>';
            }
        } else {
            $html .= '<span class="skill-tag">Belum ada keahlian yang dicantumkan</span>';
        }
        
        $html .= '</div>
                </div>';

        // References Section
        if (isset($data['references_available']) && $data['references_available'] && isset($data['references']) && !empty($data['references'])) {
            $html .= '<div class="section">
                        <div class="section-title">Referensi</div>
                        <div class="section-content">';
            
            foreach ($data['references'] as $reference) {
                $html .= '<div class="entry-item">
                            <div class="entry-title">' . htmlspecialchars($reference['name']) . '</div>
                            <div class="entry-subtitle">' . htmlspecialchars($reference['position']) . '</div>
                            <div class="entry-description">Telepon: ' . htmlspecialchars($reference['phone']) . '</div>
                          </div>';
            }
            
            $html .= '</div>
                    </div>';
        }
        
        $html .= '<div class="footer">CV dibuat pada ' . htmlspecialchars($data['created_at']) . ' | BKK SMK Negeri 1 Surabaya</div>
            </div>
        </body>
        </html>';

        return $html;
    }
    
    private function getMonthName($month) 
    {
        $months = [
            '01' => 'Januari',
            '02' => 'Februari', 
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        
        return isset($months[$month]) ? $months[$month] : $month;
    }
    
    private function getPhotoBase64($request, $alumni)
    {
        // Check if custom photo is uploaded
        if ($request->hasFile('custom_photo')) {
            $file = $request->file('custom_photo');
            $photoData = file_get_contents($file->getRealPath());
            $mimeType = $file->getMimeType();
            return 'data:' . $mimeType . ';base64,' . base64_encode($photoData);
        }
        
        // Check if alumni has a photo in storage
        if ($alumni->photo && Storage::exists('photos/' . $alumni->photo)) {
            $photoPath = storage_path('app/photos/' . $alumni->photo);
            $photoData = file_get_contents($photoPath);
            $mimeType = mime_content_type($photoPath);
            return 'data:' . $mimeType . ';base64,' . base64_encode($photoData);
        }
        
        // Return null if no photo available
        return null;
    }
}
