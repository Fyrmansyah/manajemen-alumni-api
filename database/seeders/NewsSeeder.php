<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for author_id
        $admin = Admin::where('username', 'admin@smkn1sby.sch.id')->first();
        
        if (!$admin) {
            $this->command->info('No admin user found. Please run AdminSeeder first.');
            return;
        }

        $newsData = [
            [
                'title' => 'Pembukaan Bursa Kerja Khusus SMKN 1 Surabaya 2025',
                'content' => '<p>SMKN 1 Surabaya dengan bangga mengumumkan pembukaan Bursa Kerja Khusus (BKK) untuk tahun 2025. Program ini bertujuan untuk memfasilitasi para lulusan dalam mendapatkan pekerjaan yang sesuai dengan kompetensi yang telah dipelajari selama menempuh pendidikan di sekolah.</p>

<p>BKK SMKN 1 Surabaya telah menjalin kerjasama dengan berbagai perusahaan dan instansi, baik di tingkat lokal maupun nasional, untuk menyediakan lowongan pekerjaan yang berkualitas bagi para alumni.</p>

<p>Para alumni diharapkan dapat memanfaatkan fasilitas ini dengan optimal untuk mengembangkan karir profesional mereka.</p>',
                'slug' => 'pembukaan-bursa-kerja-khusus-smkn-1-surabaya-2025',
                'status' => 'published',
                'is_featured' => true,
                'category' => 'Pengumuman',
                'author_id' => $admin->id,
                'published_at' => now(),
                'views' => 150,
            ],
            [
                'title' => 'Tips Sukses Menghadapi Interview Kerja',
                'content' => '<p>Menghadapi interview kerja merupakan salah satu tahapan penting dalam mencari pekerjaan. Berikut adalah beberapa tips yang dapat membantu para alumni dalam menghadapi interview:</p>

<h3>1. Persiapan Sebelum Interview</h3>
<ul>
<li>Pelajari profil perusahaan</li>
<li>Siapkan pertanyaan yang akan diajukan</li>
<li>Latihan menjawab pertanyaan umum interview</li>
</ul>

<h3>2. Penampilan dan Sikap</h3>
<ul>
<li>Berpakaian rapi dan profesional</li>
<li>Datang tepat waktu</li>
<li>Tunjukkan sikap yang percaya diri</li>
</ul>

<h3>3. Saat Interview</h3>
<ul>
<li>Dengarkan pertanyaan dengan baik</li>
<li>Jawab dengan jelas dan jujur</li>
<li>Tunjukkan antusiasme terhadap posisi yang dilamar</li>
</ul>',
                'slug' => 'tips-sukses-menghadapi-interview-kerja',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Tips Karir',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(2),
                'views' => 89,
            ],
            [
                'title' => 'Pelatihan Soft Skills untuk Alumni',
                'content' => '<p>SMKN 1 Surabaya akan mengadakan pelatihan soft skills untuk para alumni yang ingin meningkatkan kemampuan non-teknis mereka. Pelatihan ini akan meliputi:</p>

<h3>Materi Pelatihan:</h3>
<ul>
<li>Komunikasi Efektif</li>
<li>Teamwork dan Kolaborasi</li>
<li>Leadership dan Manajemen Waktu</li>
<li>Problem Solving</li>
<li>Adaptabilitas</li>
</ul>

<h3>Waktu dan Tempat:</h3>
<p><strong>Tanggal:</strong> 25-27 Juli 2025<br>
<strong>Waktu:</strong> 08.00 - 16.00 WIB<br>
<strong>Tempat:</strong> Aula SMKN 1 Surabaya</p>

<p>Pendaftaran dapat dilakukan melalui website BKK atau datang langsung ke kantor BKK SMKN 1 Surabaya.</p>',
                'slug' => 'pelatihan-soft-skills-untuk-alumni',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Pelatihan',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(5),
                'views' => 67,
            ],
            [
                'title' => 'Kerjasama dengan PT. Tech Innovation Indonesia',
                'content' => '<p>BKK SMKN 1 Surabaya telah menandatangani kerjasama dengan PT. Tech Innovation Indonesia untuk menyediakan lowongan pekerjaan bagi lulusan jurusan Teknik Komputer dan Jaringan serta Multimedia.</p>

<p>PT. Tech Innovation Indonesia merupakan perusahaan teknologi yang bergerak di bidang pengembangan software dan aplikasi mobile. Perusahaan ini menawarkan berbagai posisi menarik seperti:</p>

<ul>
<li>Junior Programmer</li>
<li>UI/UX Designer</li>
<li>Network Administrator</li>
<li>Quality Assurance</li>
<li>Technical Support</li>
</ul>

<p>Para alumni yang berminat dapat mengirimkan lamaran melalui sistem BKK online atau datang langsung ke kantor BKK untuk mendapatkan informasi lebih lanjut.</p>',
                'slug' => 'kerjasama-dengan-pt-tech-innovation-indonesia',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Kerjasama',
                'author_id' => $admin->id,
                'published_at' => now()->subWeek(),
                'views' => 234,
            ],
            [
                'title' => 'Success Story: Alumni TKJ Berkarir di Perusahaan Multinasional',
                'content' => '<p>Kami bangga berbagi kisah sukses salah satu alumni jurusan Teknik Komputer dan Jaringan (TKJ) angkatan 2022, Budi Santoso, yang kini berkarir sebagai Network Engineer di sebuah perusahaan multinasional.</p>

<h3>Perjalanan Karir Budi</h3>
<p>Setelah lulus dari SMKN 1 Surabaya, Budi langsung mendapatkan pekerjaan sebagai IT Support di sebuah perusahaan lokal melalui program BKK. Dengan dedikasi dan kerja keras, dalam waktu 2 tahun ia berhasil naik jabatan menjadi Junior Network Administrator.</p>

<p>Tidak puas dengan pencapaiannya, Budi terus mengembangkan kemampuannya dengan mengikuti berbagai sertifikasi IT seperti CCNA dan CompTIA Network+. Usaha kerasnya membuahkan hasil ketika ia diterima bekerja di perusahaan multinasional dengan gaji yang sangat kompetitif.</p>

<h3>Pesan untuk Adik-Adik Alumni</h3>
<p>"Jangan pernah berhenti belajar dan selalu manfaatkan fasilitas BKK yang tersedia. Networking dan pengembangan skill adalah kunci sukses di dunia kerja," pesan Budi untuk adik-adik alumninya.</p>',
                'slug' => 'success-story-alumni-tkj-berkarir-di-perusahaan-multinasional',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Success Story',
                'author_id' => $admin->id,
                'published_at' => now()->subWeeks(2),
                'views' => 156,
            ],
            [
                'title' => 'Job Fair Virtual SMKN 1 Surabaya 2025',
                'content' => '<p>Dalam rangka memfasilitasi para alumni untuk mendapatkan pekerjaan yang sesuai, BKK SMKN 1 Surabaya akan mengadakan Job Fair Virtual pada:</p>

<h3>Detail Acara:</h3>
<p><strong>Tanggal:</strong> 15-17 Agustus 2025<br>
<strong>Waktu:</strong> 09.00 - 15.00 WIB<br>
<strong>Platform:</strong> Zoom Meeting & Website BKK</p>

<h3>Perusahaan Peserta:</h3>
<ul>
<li>PT. Astra International</li>
<li>PT. Telkom Indonesia</li>
<li>PT. Bank Central Asia</li>
<li>PT. Shopee Indonesia</li>
<li>PT. Gojek Indonesia</li>
<li>Dan 20+ perusahaan lainnya</li>
</ul>

<p>Registrasi dibuka mulai 1 Agustus 2025 melalui website BKK. Jangan lewatkan kesempatan emas ini!</p>',
                'slug' => 'job-fair-virtual-smkn-1-surabaya-2025',
                'status' => 'published',
                'is_featured' => true,
                'category' => 'Pengumuman',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(1),
                'views' => 345,
            ],
            [
                'title' => 'Workshop Persiapan Sertifikasi Profesional',
                'content' => '<p>BKK SMKN 1 Surabaya mengadakan workshop persiapan sertifikasi profesional untuk membantu alumni meningkatkan kompetensi dan daya saing di dunia kerja.</p>

<h3>Sertifikasi yang Tersedia:</h3>
<ul>
<li><strong>IT:</strong> CCNA, CompTIA, Microsoft Azure</li>
<li><strong>Akuntansi:</strong> Brevet A&B, MYOB</li>
<li><strong>Marketing:</strong> Google Ads, Facebook Blueprint</li>
<li><strong>Multimedia:</strong> Adobe Certified Expert</li>
</ul>

<h3>Fasilitas:</h3>
<ul>
<li>Materi pembelajaran lengkap</li>
<li>Praktik langsung dengan tools</li>
<li>Simulasi ujian sertifikasi</li>
<li>Sertifikat keikutsertaan</li>
</ul>

<p>Biaya workshop sangat terjangkau dengan subsidi dari sekolah. Daftar sekarang juga!</p>',
                'slug' => 'workshop-persiapan-sertifikasi-profesional',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Pelatihan',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(3),
                'views' => 123,
            ],
            [
                'title' => 'Kerjasama dengan Startup Technology Hub',
                'content' => '<p>BKK SMKN 1 Surabaya menjalin kerjasama strategis dengan Technology Hub, sebuah ekosistem startup terbesar di Surabaya, untuk memberikan kesempatan magang dan kerja bagi alumni.</p>

<h3>Program Kerjasama:</h3>
<ul>
<li>Internship Program selama 6 bulan</li>
<li>Fresh Graduate Training Program</li>
<li>Startup Bootcamp</li>
<li>Mentoring dari founder startup sukses</li>
</ul>

<h3>Posisi yang Tersedia:</h3>
<ul>
<li>Junior Full Stack Developer</li>
<li>Mobile App Developer</li>
<li>Digital Marketing Specialist</li>
<li>UI/UX Designer</li>
<li>Data Analyst</li>
</ul>

<p>Program ini memberikan pengalaman kerja di lingkungan startup yang dinamis dan inovatif.</p>',
                'slug' => 'kerjasama-dengan-startup-technology-hub',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Kerjasama',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(4),
                'views' => 189,
            ],
            [
                'title' => 'Alumni Multimedia Raih Penghargaan Designer Muda Terbaik',
                'content' => '<p>Prestasi membanggakan kembali diraih oleh alumni SMKN 1 Surabaya. Sari Dewi, alumni jurusan Multimedia angkatan 2021, berhasil meraih penghargaan "Young Designer of the Year 2025" dalam ajang Indonesia Creative Awards.</p>

<h3>Pencapaian Sari Dewi:</h3>
<ul>
<li>Founder studio desain "Creative Minds"</li>
<li>Telah menangani 50+ klien nasional</li>
<li>Portofolio mencakup branding, web design, dan packaging</li>
<li>Omzet studio mencapai ratusan juta per tahun</li>
</ul>

<h3>Kunci Sukses:</h3>
<p>"Semua dimulai dari passion dan dedikasi. Ilmu yang saya dapat di SMKN 1 Surabaya menjadi pondasi kuat untuk membangun karir. Ditambah dengan terus belajar dan mengikuti perkembangan teknologi," ungkap Sari.</p>

<p>Sari juga aktif berbagi ilmu melalui workshop dan mentoring untuk adik-adik alumni yang berminat di bidang desain.</p>',
                'slug' => 'alumni-multimedia-raih-penghargaan-designer-muda-terbaik',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Success Story',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(6),
                'views' => 278,
            ],
            [
                'title' => 'Tips Membangun Personal Branding di Era Digital',
                'content' => '<p>Di era digital seperti sekarang, personal branding menjadi sangat penting untuk meningkatkan peluang karir. Berikut tips membangun personal branding yang efektif:</p>

<h3>1. Tentukan Nilai Unik Anda</h3>
<ul>
<li>Identifikasi keahlian khusus yang dimiliki</li>
<li>Tentukan nilai yang ingin ditawarkan</li>
<li>Cari differentiation point dari kompetitor</li>
</ul>

<h3>2. Bangun Presence Digital</h3>
<ul>
<li>Optimalisasi profil LinkedIn</li>
<li>Buat portfolio online yang menarik</li>
<li>Konsisten posting konten berkualitas</li>
<li>Engage dengan komunitas profesional</li>
</ul>

<h3>3. Network dan Kolaborasi</h3>
<ul>
<li>Hadiri event industri</li>
<li>Join komunitas sesuai bidang</li>
<li>Kolaborasi dengan profesional lain</li>
<li>Berbagi knowledge dan pengalaman</li>
</ul>

<p>Personal branding yang kuat akan membuka banyak peluang karir dan bisnis di masa depan.</p>',
                'slug' => 'tips-membangun-personal-branding-di-era-digital',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Tips Karir',
                'author_id' => $admin->id,
                'published_at' => now()->subWeek(),
                'views' => 156,
            ],
            [
                'title' => 'Program Entrepreneur Muda untuk Alumni',
                'content' => '<p>BKK SMKN 1 Surabaya meluncurkan program khusus "Entrepreneur Muda" untuk mendorong alumni yang ingin memulai bisnis sendiri. Program ini didukung oleh Kementerian Koperasi dan UKM.</p>

<h3>Fasilitas Program:</h3>
<ul>
<li>Pelatihan business plan dan finansial</li>
<li>Mentoring dari pengusaha sukses</li>
<li>Akses ke modal usaha mikro</li>
<li>Networking dengan investor angel</li>
<li>Incubation space selama 1 tahun</li>
</ul>

<h3>Kriteria Peserta:</h3>
<ul>
<li>Alumni SMKN 1 Surabaya</li>
<li>Memiliki ide bisnis yang jelas</li>
<li>Berkomitmen mengikuti program 6 bulan</li>
<li>Bersedia bermitra dengan sekolah</li>
</ul>

<h3>Timeline Program:</h3>
<p><strong>Pendaftaran:</strong> 1-31 Agustus 2025<br>
<strong>Seleksi:</strong> 1-7 September 2025<br>
<strong>Program dimulai:</strong> 15 September 2025</p>

<p>Kuota terbatas hanya 20 peserta. Daftar segera!</p>',
                'slug' => 'program-entrepreneur-muda-untuk-alumni',
                'status' => 'published',
                'is_featured' => true,
                'category' => 'Program',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(8),
                'views' => 445,
            ],
            [
                'title' => 'Panduan Lengkap Menulis CV yang Menarik',
                'content' => '<p>CV (Curriculum Vitae) adalah dokumen pertama yang dilihat oleh recruiter. Berikut panduan lengkap menulis CV yang menarik dan profesional:</p>

<h3>1. Format dan Layout</h3>
<ul>
<li>Gunakan font yang mudah dibaca (Arial, Calibri)</li>
<li>Maksimal 2 halaman untuk fresh graduate</li>
<li>Konsisten dalam penggunaan bullet points</li>
<li>Beri white space yang cukup</li>
</ul>

<h3>2. Informasi Wajib</h3>
<ul>
<li>Data pribadi (nama, kontak, alamat)</li>
<li>Professional summary/objective</li>
<li>Pengalaman kerja/magang</li>
<li>Pendidikan</li>
<li>Skills dan kompetensi</li>
<li>Portofolio (jika relevan)</li>
</ul>

<h3>3. Tips Menulis yang Efektif</h3>
<ul>
<li>Sesuaikan dengan posisi yang dilamar</li>
<li>Gunakan action words (managed, developed, created)</li>
<li>Sertakan achievement dengan angka</li>
<li>Hindari typo dan grammatical error</li>
</ul>

<h3>4. Kesalahan yang Harus Dihindari</h3>
<ul>
<li>Foto yang tidak profesional</li>
<li>Informasi yang tidak relevan</li>
<li>Format yang berantakan</li>
<li>Berbohong tentang pengalaman</li>
</ul>

<p>CV yang baik adalah investasi untuk masa depan karir Anda!</p>',
                'slug' => 'panduan-lengkap-menulis-cv-yang-menarik',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Tips Karir',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(10),
                'views' => 234,
            ],
            [
                'title' => 'Kerjasama dengan Bank untuk Program Credit Scoring Alumni',
                'content' => '<p>BKK SMKN 1 Surabaya menjalin kerjasama dengan Bank ABC untuk memberikan kemudahan akses kredit kepada alumni yang telah bekerja. Program ini bertujuan membantu alumni dalam pengembangan karir dan bisnis.</p>

<h3>Keuntungan Program:</h3>
<ul>
<li>Proses approval yang lebih cepat</li>
<li>Suku bunga preferential</li>
<li>Syarat yang lebih mudah</li>
<li>Konsultasi finansial gratis</li>
</ul>

<h3>Jenis Kredit yang Tersedia:</h3>
<ul>
<li>Kredit Tanpa Agunan (KTA)</li>
<li>Kredit Kendaraan Bermotor</li>
<li>Kredit Usaha Mikro</li>
<li>Kredit Pendidikan lanjutan</li>
</ul>

<h3>Syarat dan Ketentuan:</h3>
<ul>
<li>Alumni SMKN 1 Surabaya</li>
<li>Telah bekerja minimal 1 tahun</li>
<li>Memiliki slip gaji tetap</li>
<li>Tidak ada riwayat kredit macet</li>
</ul>

<p>Konsultasi dan pendaftaran dapat dilakukan di kantor BKK atau melalui website resmi.</p>',
                'slug' => 'kerjasama-dengan-bank-untuk-program-credit-scoring-alumni',
                'status' => 'published',
                'is_featured' => false,
                'category' => 'Kerjasama',
                'author_id' => $admin->id,
                'published_at' => now()->subDays(12),
                'views' => 167,
            ],
        ];

        foreach ($newsData as $news) {
            News::create($news);
        }

        $this->command->info('News seeded successfully!');
    }
}
