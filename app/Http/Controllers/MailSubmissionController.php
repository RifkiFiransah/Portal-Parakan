<?php

namespace App\Http\Controllers;

use App\Models\MailSubmission;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MailSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            $mails = MailSubmission::where('user_id', Auth::id())->latest()->paginate(10);
        } else {
            $mails = MailSubmission::latest()->paginate(10);
        }
        $totalmails = $mails->count();
        $pendingmails = $mails->where('status', 'pending')->count();
        $resolvedmails = $mails->where('status', 'completed')->count();
        $processmails = $mails->where('status', 'process')->count();
        return view('backend.admin.mailsubmission.index', compact('mails', 'pendingmails', 'resolvedmails', 'processmails', 'totalmails'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.mailsubmission.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'jenis_surat' => 'required|string|in:tidak_mampu,usaha,domisili,belum_menikah',
            'name' => 'required|string|max:255',
            'no_kk' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:30',
            'no_hp' => 'nullable|string|max:15',
            'description' => 'nullable|string',
        ];

        switch ($request->jenis_surat) {
            case 'tidak_mampu':
                $rules = array_merge($rules, [
                    'tempat_lahir'    => 'required|string|max:255',
                    'tanggal_lahir'   => 'required|date', // gunakan input[type=date]
                    'jenis_kelamin'   => 'required|in:Laki-laki,Perempuan',
                    'nama_sekolah'    => 'required|string|max:255',
                    'nama_ortu'       => 'required|string|max:255',
                    'pekerjaan_ortu'  => 'required|string|max:255',
                    'alamat'          => 'required|string',
                ]);
                break;

            case 'usaha':
                $rules = array_merge($rules, [
                    'tempat_lahir'    => 'required|string|max:255',
                    'tanggal_lahir'   => 'required|date',
                    'pekerjaan'       => 'required|string|max:255',
                    'alamat'          => 'required|string',
                    'jenis_usaha'     => 'required|string|max:255',
                ]);
                break;

            case 'domisili':
                $rules = array_merge($rules, [
                    'tempat_lahir'     => 'required|string|max:255',
                    'tanggal_lahir'    => 'required|date',
                    'nik'              => 'required|string',
                    'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
                    'alamat_ktp'       => 'required|string',
                    'alamat_domisili'  => 'required|string',
                ]);
                break;

            case 'belum_menikah':
                $rules = array_merge($rules, [
                    'tempat_lahir'    => 'required|string|max:255',
                    'tanggal_lahir'   => 'required|date',
                    'jenis_kelamin'   => 'required|in:Laki-laki,Perempuan',
                    'alamat'          => 'required|string',
                ]);
                break;

            default:
                $rules = array_merge($rules, [
                    'description' => 'required|string',
                ]);
        }

        $data = $request->validate($rules);

        // Ubah short key ke label lengkap (opsional)
        $map = [
            'tidak_mampu'   => 'Surat Keterangan Tidak Mampu',
            'usaha'         => 'Surat Keterangan Usaha',
            'domisili'      => 'Surat Keterangan Domisili',
            'belum_menikah' => 'Surat Keterangan Belum Menikah',
        ];
        $data['jenis_surat'] = $map[$data['jenis_surat']] ?? $data['jenis_surat'];

        $data['user_id'] = Auth::id();

        $createMail = MailSubmission::create($data);

        if (!$createMail) {
            return redirect()->back()->with('error', 'Gagal membuat pengajuan surat.');
        }

        return redirect()->route('mail-submissions.index')->with('success', 'Pengajuan surat berhasil dibuat.');
    }


    /**
     * Display the specified resource.
     */
    public function show(MailSubmission $mailSubmission)
    {
        return view('backend.admin.mailsubmission.show', compact('mailSubmission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MailSubmission $mailSubmission)
    {
        return view('backend.admin.mailsubmission.edit', compact('mailSubmission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MailSubmission $mailSubmission)
    {
        // Gunakan logika validasi yang sama seperti store()
        $rules = [
            'jenis_surat' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ];

        switch ($request->jenis_surat) {
            case 'Surat Keterangan Tidak Mampu':
                $rules = array_merge($rules, [
                    'tempat_lahir' => 'required|string|max:255',
                    'tanggal_lahir' => 'required|date',
                    'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                    'nama_sekolah' => 'required|string|max:255',
                    'nama_ortu' => 'required|string|max:255',
                    'pekerjaan_ortu' => 'required|string|max:255',
                    'alamat' => 'required|string',
                ]);
                break;

            case 'Surat Keterangan Usaha':
                $rules = array_merge($rules, [
                    'tempat_lahir' => 'required|string|max:255',
                    'tanggal_lahir' => 'required|date',
                    'pekerjaan' => 'required|string|max:255',
                    'alamat' => 'required|string',
                    'jenis_usaha' => 'required|string|max:255',
                ]);
                break;

            case 'Surat Keterangan Domisili':
                $rules = array_merge($rules, [
                    'tempat_lahir' => 'required|string|max:255',
                    'tanggal_lahir' => 'required|date',
                    'nik' => 'required|numeric',
                    'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                    'alamat_ktp' => 'required|string',
                    'alamat_domisili' => 'required|string',
                ]);
                break;

            case 'Surat Keterangan Belum Menikah':
                $rules = array_merge($rules, [
                    'tempat_lahir' => 'required|string|max:255',
                    'tanggal_lahir' => 'required|date',
                    'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                    'alamat' => 'required|string',
                ]);
                break;

            default:
                $rules = array_merge($rules, [
                    'description' => 'required|string',
                ]);
        }

        $data = $request->validate($rules);
        $data['user_id'] = Auth::id();

        $updateMail = $mailSubmission->update($data);

        if (!$updateMail) {
            return redirect()->back()->with('error', 'Gagal memperbarui pengajuan surat.');
        }

        return redirect()->route('mail-submissions.index')->with('success', 'Pengajuan surat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MailSubmission $mailSubmission)
    {
        // Delete file if exists
        if ($mailSubmission->file && Storage::disk('public')->exists($mailSubmission->file)) {
            Storage::disk('public')->delete($mailSubmission->file);
        }

        $mailSubmission->delete();

        return redirect()->route('mail-submissions.index')->with('success', 'Mail submission deleted successfully.');
    }

    /**
     * Update status of mail submission
     */
    public function updateStatus(Request $request, MailSubmission $mailSubmission)
    {
        $request->validate([
            'status' => 'required|in:pending,process,completed'
        ]);

        $mailSubmission->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status berhasil diperbarui');
    }

    /**
     * Generate PDF file for mail submission
     */
    public function generatePdf(MailSubmission $mailSubmission)
    {
        try {
            // Create PDF content based on jenis_surat
            $pdfContent = $this->generatePdfContent($mailSubmission);

            // Configure Dompdf
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);

            // Create Dompdf instance
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($pdfContent);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Generate filename
            $fileName = 'surat_' . strtolower(str_replace(' ', '_', $mailSubmission->jenis_surat)) . '_' . $mailSubmission->id . '_' . date('Ymd') . '.pdf';

            // Save PDF to storage
            $pdfOutput = $dompdf->output();
            $filePath = 'mail_documents/' . $fileName;
            Storage::disk('public')->put($filePath, $pdfOutput);

            // Update mail submission with file path
            $mailSubmission->update([
                'file' => $filePath,
                'status' => 'completed'
            ]);

            return redirect()->back()->with('success', 'File PDF berhasil dibuat dan status diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat file PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download PDF file
     */
    public function downloadPdf(MailSubmission $mailSubmission)
    {
        if (!$mailSubmission->file || !Storage::disk('public')->exists($mailSubmission->file)) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan');
        }

        $fileName = basename($mailSubmission->file);
        $filePath = storage_path('app/public/' . $mailSubmission->file);

        return response()->download($filePath, $fileName);
    }

    /**
     * Generate PDF content based on jenis_surat
     */
    private function generatePdfContent(MailSubmission $mailSubmission)
    {
        $date = Carbon::now()->locale('id')->isoFormat('D MMMM Y');

        // Ambil gambar dari public dan ubah ke Base64
        $logoPath = public_path('assets/img/logo-garuda.png');
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoBase64 = 'data:image/png;base64,' . $logoData;

        $header = '
        <div class="center">
            <img src="' . $logoBase64 . '" alt="Logo Garuda" style="width:90px; height:auto; margin-bottom:10px;">
        </div>
        <div style="text-align: center; margin-bottom: 5px;">
            <h2 style="margin: 0; font-size: 18px; font-weight: bold;">KEPALA DESA PARAKAN</h2>
            <h2 style="margin: 5px 0; font-size: 18px; font-weight: bold;">KECAMATAN MALEBER KABUPATEN KUNINGAN</h2>
        </div>
        <hr style="border: 2px solid black; margin-top:4px; margin-bottom:0;">
        <hr style="border: 1px solid black; margin-top:1px; margin-bottom:20px;">
        ';

        switch ($mailSubmission->jenis_surat) {
            case 'Surat Keterangan Domisili':
                return $this->generateDomisiliPdf($mailSubmission, $header, $date);
            case 'Surat Keterangan Usaha':
                return $this->generateUsahaPdf($mailSubmission, $header, $date);
            case 'Surat Keterangan Tidak Mampu':
                return $this->generateTidakMampuPdf($mailSubmission, $header, $date);
            case 'Surat Keterangan Kematian':
                return $this->generateKematianPdf($mailSubmission, $header, $date);
            case 'Surat Keterangan Lahir':
                return $this->generateLahirPdf($mailSubmission, $header, $date);
            case 'Surat Keterangan Pindah':
                return $this->generatePindahPdf($mailSubmission, $header, $date);
            case 'Surat Keterangan Belum Menikah':
                return $this->generateBelumMenikahPdf($mailSubmission, $header, $date);
            case 'Surat Keterangan Cerai':
                return $this->generateCeraiPdf($mailSubmission, $header, $date);
            default:
                return $this->generateTidakMampuPdf($mailSubmission, $header, $date);
        }
    }


    private function generateDomisiliPdf($mailSubmission, $header, $date)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8" />
            <title>Surat Keterangan Domisili</title>
            <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.5;
                margin: 40px;
            }
            .center {
                text-align: center;
            }
            .bold {
                font-weight: bold;
            }
            .underline {
                text-decoration: underline;
            }
            .mb-10 {
                margin-bottom: 10px;
            }
            .mb-20 {
                margin-bottom: 20px;
            }
            .indent {
                margin-left: 40px;
            }
            </style>
        </head>
        <body>
            ' . $header . '

            <div class="center mb-20">
            <h3 class="bold underline">SURAT KETERANGAN DOMISILI</h3>
            <p>Nomor: ......................................</p>
            </div>

            <p class="mb-10">
            Yang bertandatangan dibawah ini, Kepala Desa Parakan Kecamatan Maleber Kabupaten Kuningan, dengan ini menerangkan dengan sesungguhnya, bahwa :
            </p>

            <div class="indent mb-20">
            <table style="width: 100%">
                <tr>
                <td style="width: 150px">Nama</td>
                <td style="width: 10px">:</td>
                <td><strong>' . $mailSubmission->name . '</strong></td>
                </tr>
                <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>' . $mailSubmission->tempat_lahir . ', ' . $mailSubmission->tanggal_lahir . '</td>
                </tr>
                <tr>
                <td>NIK</td>
                <td>:</td>
                <td>' . $mailSubmission->nik . '</td>
                </tr>
                <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>' . $mailSubmission->jenis_kelamin . '</td>
                </tr>
                <tr>
                <td>Alamat KTP</td>
                <td>:</td>
                <td>' . $mailSubmission->alamat_ktp . '</td>
                </tr>
            </table>
            </div>

            <p class="mb-10">
            Adalah Benar Warga dilingkungan kami yang sampai saat ini tinggal berdomisili di '. $mailSubmission->alamat_domisili .'.
            </p>

            <p class="mb-10">
            Demikian surat Keterangan ini dibuat, untuk dapat dipergunakan sebagaimana mestinya
            </p>


            <div
            style="float: right; width: 200px; text-align: center; margin-top: 30px"
            >
            <p>Parakan, ' . $date . '</p>
            <p class="bold">Kepala Desa Parakan</p>
            <br /><br /><br />
            <p class="bold underline">Muhammad Tohir</p>
            </div>
        </body>
        </html>
';
    }

    private function generateUsahaPdf($mailSubmission, $header, $date)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8" />
            <title>Surat Keterangan Usaha</title>
            <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.5;
                margin: 40px;
            }
            .center {
                text-align: center;
            }
            .bold {
                font-weight: bold;
            }
            .underline {
                text-decoration: underline;
            }
            .mb-10 {
                margin-bottom: 10px;
            }
            .mb-20 {
                margin-bottom: 20px;
            }
            .indent {
                margin-left: 40px;
            }
            </style>
        </head>
        <body>
            ' . $header . '

            <div class="center mb-20">
            <h3 class="bold underline">SURAT KETERANGAN USAHA</h3>
            <p>Nomor: ...........................</p>
            </div>

            <p class="mb-10">
            Yang bertanda tangan dibawah ini, Kepala Desa Parakan Kecamatan Maleber, Kabupaten Kuningan menerangkan bahwa :
            </p>

            <div class="indent mb-20">
            <table style="width: 100%">
                <tr>
                <td style="width: 150px">Nama</td>
                <td style="width: 10px">:</td>
                <td><strong>' . $mailSubmission->name . '</strong></td>
                </tr>
                <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>' . $mailSubmission->tempat_lahir . ', ' . $mailSubmission->tanggal_lahir . '</td>
                </tr>
                <tr>
                <td>Pekerjaan</td>
                <td>:</td>
                <td>' . $mailSubmission->pekerjaan . '</td>
                </tr>
                <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>' . $mailSubmission->alamat . '</td>
                </tr>
            </table>
            </div>

            <p class="mb-10">
            Yang bersangkutan mempunyai pekerjaan tetap sebagai : '. $mailSubmission->jenis_usaha .'
            </p>

            <p class="mb-20">
            Sepanjang penelitian kami, baik sifat maupun tempat usahanya tidak mengganggu ketertiban umum dan tidak bertentangan dengan Peraturan Pemerintah setempat.
            </p>
            
            <p class="mb-20">
            Demikian Surat Keterangan Usaha ini kami buat dengan sebenarnya untuk dipergunakan seperlunya.
            </p>

            <div
            style="float: right; width: 200px; text-align: center; margin-top: 30px"
            >
            <p>Parakan, ' . $date . '</p>
            <p class="bold">Kepala Desa Parakan</p>
            <br /><br /><br />
            <p class="bold underline">Muhammad Tohir</p>
            </div>
        </body>
        </html>
';
    }

    private function generateBelumMenikah($mailSubmission, $header, $date)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8" />
            <title>Surat Keterangan Belum Menikah</title>
            <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.5;
                margin: 40px;
            }
            .center {
                text-align: center;
            }
            .bold {
                font-weight: bold;
            }
            .underline {
                text-decoration: underline;
            }
            .mb-10 {
                margin-bottom: 10px;
            }
            .mb-20 {
                margin-bottom: 20px;
            }
            .indent {
                margin-left: 40px;
            }
            </style>
        </head>
        <body>
            ' . $header . '

            <div class="center mb-20">
            <h3 class="bold underline">SURAT KETERANGAN Belum Menikah</h3>
            <p>Nomor: ...........................</p>
            </div>

            <p class="mb-10">
            Yang bertanda tangan dibawah ini, Kepala Desa Parakan Kecamatan Maleber,
            Kabupaten Kuningan menerangkan bahwa :
            </p>

            <div class="indent mb-20">
            <table style="width: 100%">
                <tr>
                <td style="width: 150px">Nama</td>
                <td style="width: 10px">:</td>
                <td><strong>' . $mailSubmission->name . '</strong></td>
                </tr>
                <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>
                    ' . $mailSubmission->tempat_lahir . ', ' .
                    $mailSubmission->tanggal_lahir . '
                </td>
                </tr>
                <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>' . $mailSubmission->jenis_kelamin . '</td>
                </tr>
                <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>' . $mailSubmission->alamat . '</td>
                </tr>
            </table>
            </div>

            <p class="mb-10">
            Orang tersebut diatas adalah benar penduduk desa kami. dan sampai saat ini orang tersebut 
            <strong>belum pernah menikah</strong>
            </p>

            <p class="mb-20">
            Demikian Keterangan ini kami buat dengan sebenarnya dan kepada yang berkepentingan agar dapat
            dijadikan bahan pertimbangan sebagaimana mestinya.
            </p>

            <div
            style="float: right; width: 200px; text-align: center; margin-top: 30px"
            >
            <p>Parakan, ' . $date . '</p>
            <p class="bold">Kepala Desa Parakan</p>
            <br /><br /><br />
            <p class="bold underline">Muhammad Tohir</p>
            </div>
        </body>
        </html>

        ';
    }

    // Tambahkan method untuk jenis surat lainnya jika diperlukan
    private function generateTidakMampuPdf($mailSubmission, $header, $date)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8" />
            <title>Surat Keterangan Tidak Mampu</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.5;
                    margin: 40px;
                }
                .center {
                    text-align: center;
                }
                .bold {
                    font-weight: bold;
                }
                .underline {
                    text-decoration: underline;
                }
                .mb-10 {
                    margin-bottom: 10px;
                }
                .mb-20 {
                    margin-bottom: 20px;
                }
                .mt-20 {
                    margin-top: 100px;
                }
                .indent {
                    margin-left: 40px;
                }
                /* Footer agar selalu di bawah */
                .footer {
                    position: fixed;
                    margin: auto;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    padding: 10px 0;
                }
                .footer p {
                    margin: 0;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    font-size: 11px;
                }
            </style>
        </head>
        <body>
            '. $header .'

            <div class="center mb-20">
                <h3 class="bold underline">SURAT KETERANGAN TIDAK MAMPU</h3>
                <p>Nomor: ..................................</p>
            </div>

            <p class="mb-10">
                Yang bertanda tangan di bawah ini, Kepala Desa Parakan Kecamatan Maleber Kabupaten Kuningan, menerangkan bahwa :
            </p>

            <div class="indent mb-20">
                <table style="width: 100%">
                    <tr>
                        <td style="width: 150px">Nama</td>
                        <td style="width: 10px">:</td>
                        <td><strong>'. $mailSubmission->name .'</strong></td>
                    </tr>
                    <tr>
                        <td>Tempat Tanggal Lahir</td>
                        <td>:</td>
                        <td>'. $mailSubmission->tempat .' '. $mailSubmission->tanggal_lahir .'</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>'. $mailSubmission->jenis_kelamin .'</td>
                    </tr>
                    <tr>
                        <td>Nama Sekolah</td>
                        <td>:</td>
                        <td>'. $mailSubmission->nama_sekolah .'</td>
                    </tr>
                </table>
            </div>

            <p class="mb-10">
                Yang bersangkutan adalah anak dari :
            </p>

            <div class="indent mb-20">
                <table style="width: 100%">
                    <tr>
                        <td style="width: 150px">Nama</td>
                        <td style="width: 10px">:</td>
                        <td><strong>'. $mailSubmission->nama_ortu .'</strong></td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>'. $mailSubmission->pekerjaan_ortu .'</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>'. $mailSubmission->alamat .'</td>
                    </tr>
                </table>
            </div>

            <p class="mb-10">
                Orang tersebut diatas adalah benar, dari keluarga keadaan ekonominya tidak mampu.  
                Demikian surat keterangan ini kami buat, agar dapat dipergunakan sebagaimana mestinya
            </p>

            <div style="float: right; width: 200px; text-align: center; margin-top: 30px">
                <p>Parakan, '. $date .'</p>
                <p class="bold">Kepala Desa Parakan</p>
                <br /><br /><br />
                <p class="bold underline">MUHAMMAD_TOHIR</p>
            </div>

            <!-- Footer tetap di bawah -->
            <div class="footer center">
                Jalan Raya Parakan No 220 No Telp 087771219240<br>
                KUNINGAN 45575
            </div>
        </body>
        </html>
        ';
    }

    private function generateKematianPdf($mailSubmission, $header, $date)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Surat Keterangan Kematian</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.5; margin: 40px; }
                .center { text-align: center; }
                .bold { font-weight: bold; }
                .underline { text-decoration: underline; }
                .mb-10 { margin-bottom: 10px; }
                .mb-20 { margin-bottom: 20px; }
                .indent { margin-left: 40px; }
            </style>
        </head>
        <body>
            ' . $header . '
            
            <div class="center mb-20">
                <h3 class="bold underline">SURAT KETERANGAN KEMATIAN</h3>
                <p>Nomor: [NOMOR_SURAT]</p>
            </div>
            
            <p class="mb-10">Yang bertanda tangan di bawah ini, Kepala Desa Parakan, menerangkan bahwa:</p>
            
            <div class="indent mb-20">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 150px;">Nama Almarhum/Almarhumah</td>
                        <td style="width: 10px;">:</td>
                        <td><strong>' . $mailSubmission->name . '</strong></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>' . $mailSubmission->nik . '</td>
                    </tr>
                    <tr>
                        <td>No. KK</td>
                        <td>:</td>
                        <td>' . $mailSubmission->no_kk . '</td>
                    </tr>
                    <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td>' . $mailSubmission->description . '</td>
                    </tr>
                </table>
            </div>
            
            <p class="mb-10">Adalah benar-benar penduduk Desa Parakan yang telah <strong>MENINGGAL DUNIA</strong>.</p>
            
            <p class="mb-20">Demikian surat keterangan ini dibuat dengan sebenar-benarnya dan dapat dipergunakan sebagaimana mestinya.</p>
            
            <div style="float: right; width: 200px; text-align: center; margin-top: 30px;">
                <p>Parakan, ' . $date . '</p>
                <p class="bold">Kepala Desa Parakan</p>
                <br><br><br>
                <p class="bold underline">Muhammad Tohir</p>
            </div>
        </body>
        </html>';
    }

    private function generateLahirPdf($mailSubmission, $header, $date)
    {
        return $this->generateTidakMampuPdf($mailSubmission, $header, $date);
    }
    private function generatePindahPdf($mailSubmission, $header, $date)
    {
        return $this->generateTidakMampuPdf($mailSubmission, $header, $date);
    }
    private function generateBelumMenikahPdf($mailSubmission, $header, $date)
    {
        return $this->generateBelumMenikah($mailSubmission, $header, $date);
    }
    private function generateCeraiPdf($mailSubmission, $header, $date)
    {
        return $this->generateTidakMampuPdf($mailSubmission, $header, $date);
    }
}
