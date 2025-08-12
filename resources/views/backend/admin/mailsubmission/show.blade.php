@extends('backend.admin.layouts.app')

@section('title', 'Detail Pengajuan Surat - Portal Parakan')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Portal Parakan / Pengajuan Surat /</span> Detail Pengajuan Surat
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('mail-submissions.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-detail me-2"></i>Detail Pengajuan Surat
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informasi Pemohon -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Informasi Pemohon</h6>
                            <table class="table table-borderless">
                                <tr><td class="fw-semibold">Nama:</td><td>{{ $mailSubmission->name }}</td></tr>
                                <tr><td class="fw-semibold">NIK:</td><td>{{ $mailSubmission->nik }}</td></tr>
                                <tr><td class="fw-semibold">No. KK:</td><td>{{ $mailSubmission->no_kk }}</td></tr>
                                <tr><td class="fw-semibold">No. HP:</td><td>{{ $mailSubmission->no_hp ?? '-' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Informasi Surat</h6>
                            <table class="table table-borderless">
                                <tr><td class="fw-semibold">Jenis Surat:</td><td>{{ $mailSubmission->jenis_surat }}</td></tr>
                                <tr>
                                    <td class="fw-semibold">Status:</td>
                                    <td>
                                        @if ($mailSubmission->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($mailSubmission->status == 'process')
                                            <span class="badge bg-info">Diproses</span>
                                        @else
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr><td class="fw-semibold">Dibuat:</td><td>{{ $mailSubmission->created_at->format('d M Y H:i') }}</td></tr>
                                <tr><td class="fw-semibold">File:</td>
                                    <td>
                                        @if ($mailSubmission->file)
                                            <span class="badge bg-success"><i class="bx bx-check me-1"></i>Tersedia</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bx bx-x me-1"></i>Belum ada</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Detail Tambahan Berdasarkan Jenis Surat -->
                    @if($mailSubmission->jenis_surat)
                        <h6 class="text-muted mt-3">Detail Tambahan</h6>
                        <table class="table table-borderless">
                            @switch(strtolower($mailSubmission->jenis_surat))
                                @case('surat keterangan domisili')
                                    <tr><td class="fw-semibold">Alamat Domisili:</td><td>{{ $mailSubmission->alamat_domisili }}</td></tr>
                                    <tr><td class="fw-semibold">RT / RW:</td><td>{{ $mailSubmission->rt_rw }}</td></tr>
                                    @break

                                @case('surat keterangan usaha')
                                    <tr><td class="fw-semibold">Nama Usaha:</td><td>{{ $mailSubmission->nama_usaha }}</td></tr>
                                    <tr><td class="fw-semibold">Alamat Usaha:</td><td>{{ $mailSubmission->alamat_usaha }}</td></tr>
                                    @break

                                @case('surat keterangan tidak mampu')
                                    <tr><td class="fw-semibold">Keperluan:</td><td>{{ $mailSubmission->keperluan }}</td></tr>
                                    @break

                                @case('surat keterangan kematian')
                                    <tr><td class="fw-semibold">Nama Almarhum:</td><td>{{ $mailSubmission->nama_almarhum }}</td></tr>
                                    <tr><td class="fw-semibold">Tanggal Meninggal:</td><td>{{ \Carbon\Carbon::parse($mailSubmission->tanggal_meninggal)->format('d M Y') }}</td></tr>
                                    @break

                                @case('surat keterangan lahir')
                                    <tr><td class="fw-semibold">Nama Anak:</td><td>{{ $mailSubmission->nama_anak }}</td></tr>
                                    <tr><td class="fw-semibold">Tanggal Lahir:</td><td>{{ \Carbon\Carbon::parse($mailSubmission->tanggal_lahir)->format('d M Y') }}</td></tr>
                                    <tr><td class="fw-semibold">Tempat Lahir:</td><td>{{ $mailSubmission->tempat_lahir }}</td></tr>
                                    @break

                                @case('surat keterangan pindah')
                                    <tr><td class="fw-semibold">Alamat Tujuan Pindah:</td><td>{{ $mailSubmission->alamat_pindah }}</td></tr>
                                    <tr><td class="fw-semibold">Tanggal Pindah:</td><td>{{ \Carbon\Carbon::parse($mailSubmission->tanggal_pindah)->format('d M Y') }}</td></tr>
                                    @break

                                @case('surat keterangan belum menikah')
                                    <tr><td class="fw-semibold">Keperluan:</td><td>{{ $mailSubmission->keperluan }}</td></tr>
                                    @break

                                @case('surat keterangan cerai')
                                    <tr><td class="fw-semibold">Nama Mantan Pasangan:</td><td>{{ $mailSubmission->nama_mantan }}</td></tr>
                                    <tr><td class="fw-semibold">Tanggal Perceraian:</td><td>{{ \Carbon\Carbon::parse($mailSubmission->tanggal_cerai)->format('d M Y') }}</td></tr>
                                    @break
                            @endswitch
                        </table>
                    @endif

                    <!-- Keterangan Tambahan -->
                    @if($mailSubmission->description)
                        <div class="mb-4">
                            <h6 class="text-muted">Keterangan Tambahan</h6>
                            <div class="p-3 bg-light rounded">
                                {!! $mailSubmission->description !!}
                            </div>
                        </div>
                    @endif

                    <!-- Form Update Status untuk Admin -->
                    @if (Auth::user()->role == 'admin')
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white mb-4">
                                <i class="bx bx-cog me-2"></i>Ubah Status
                            </div>
                            <div class="card-body">
                                <form action="{{ route('mail-submissions.update-status', $mailSubmission->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <select class="form-select" name="status" required>
                                                <option value="pending" {{ $mailSubmission->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="process" {{ $mailSubmission->status == 'process' ? 'selected' : '' }}>Diproses</option>
                                                <option value="completed" {{ $mailSubmission->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bx bx-save me-2"></i>Update Status
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><i class="bx bx-info-circle me-2"></i>Informasi Pengajuan Surat</div>
                <div class="card-body">
                    <p><strong>ID Pengajuan:</strong> #{{ $mailSubmission->id }}</p>
                    <p><strong>Status:</strong>
                        @if ($mailSubmission->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($mailSubmission->status == 'process')
                            <span class="badge bg-info">Diproses</span>
                        @else
                            <span class="badge bg-success">Selesai</span>
                        @endif
                    </p>
                    <p><strong>Dibuat:</strong> {{ $mailSubmission->created_at->format('d M Y H:i') }}</p>
                    <p><strong>Diperbarui:</strong> {{ $mailSubmission->updated_at->format('d M Y H:i') }}</p>
                    @if ($mailSubmission->file)
                        <p><strong>File PDF:</strong> <span class="badge bg-success">Tersedia</span></p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="bx bx-cog me-2"></i>Aksi</div>
                <div class="card-body">
                    @if (Auth::user()->role == 'admin')
                        @if ($mailSubmission->file)
                            <a href="{{ route('mail-submissions.download-pdf', $mailSubmission->id) }}" class="btn btn-primary w-100 mb-2">
                                <i class="bx bx-download me-2"></i>Download File PDF
                            </a>
                            <form action="{{ route('mail-submissions.generate-pdf', $mailSubmission->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-success w-100 mb-2" onclick="return confirm('Buat ulang PDF?')">
                                    <i class="bx bx-refresh me-2"></i>Buat Ulang PDF
                                </button>
                            </form>
                        @else
                            <form action="{{ route('mail-submissions.generate-pdf', $mailSubmission->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Buat PDF?')">
                                    <i class="bx bx-file-blank me-2"></i>Buat File PDF
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('mail-submissions.edit', $mailSubmission->id) }}" class="btn btn-warning w-100 mb-2">
                            <i class="bx bx-edit me-2"></i>Edit Pengajuan Surat
                        </a>

                        <button type="button" class="btn btn-outline-danger w-100" onclick="confirmDelete({{ $mailSubmission->id }}, '{{ $mailSubmission->name }} - {{ $mailSubmission->jenis_surat }}')">
                            <i class="bx bx-trash me-2"></i>Hapus Pengajuan Surat
                        </button>
                    @else
                        @if ($mailSubmission->file)
                            <a href="{{ route('mail-submissions.download-pdf', $mailSubmission->id) }}" class="btn btn-primary w-100">
                                <i class="bx bx-download me-2"></i>Download File PDF
                            </a>
                        @else
                            <div>File Surat Belum Dibuatkan oleh Desa</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id, title) {
            if (confirm(`Apakah Anda yakin ingin menghapus "${title}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/mail-submissions/${id}`;
                form.innerHTML = `
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
