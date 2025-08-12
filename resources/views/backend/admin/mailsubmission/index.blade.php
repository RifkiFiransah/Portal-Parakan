@extends('backend.admin.layouts.app')

@section('title', 'Kelola Pengajuan Surat - Portal Parakan')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Portal Parakan / Pengajuan Surat /</span> Semua Pengajuan Surat
        </h4>
    </div>
@endsection

@section('content')
    <!-- Filter and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('mail-submissions.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Cari Pengajuan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                placeholder="Nama / NIK / Jenis Surat...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="process" {{ request('status') == 'process' ? 'selected' : '' }}>Proses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <i class="bx bx-file bx-md text-primary"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Total Pengajuan</span>
                            <h3 class="card-title mb-0">{{ $totalmails ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <i class="bx bx-refresh bx-md text-secondary"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Pending</span>
                            <h3 class="card-title mb-0">{{ $pendingmails ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <i class="bx bx-edit bx-md text-warning"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Proses</span>
                            <h3 class="card-title mb-0">{{ $processmails ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <i class="bx bx-check-circle bx-md text-success"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Selesai</span>
                            <h3 class="card-title mb-0">{{ $resolvedmails ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- List Pengajuan Surat -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengajuan Surat</h5>
            @if (Auth::user()->role == 'admin')
                <a href="{{ route('mail-submissions.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-2"></i>Tambah Pengajuan Surat
                </a>
            @endif
        </div>
        <div class="card-body">
            @if ($mails->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NIK</th>
                                <th>No. KK</th>
                                <th>Nama</th>
                                <th>Jenis Surat</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>File</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mails as $mail)
                                <tr>
                                    <td><strong>#{{ $mail->id }}</strong></td>
                                    <td>{{ $mail->nik }}</td>
                                    <td>{{ $mail->no_kk }}</td>
                                    <td>{{ $mail->name }}</td>
                                    <td>{{ $mail->jenis_surat }}</td>
                                    <td><small class="text-muted">{{ Str::limit(strip_tags($mail->description), 50) }}</small></td>
                                    <td>
                                        @if ($mail->status == 'pending')
                                            <span class="badge bg-warning"><i class="bx bx-time me-1"></i>Pending</span>
                                        @elseif($mail->status == 'process')
                                            <span class="badge bg-info"><i class="bx bx-loader-circle me-1"></i>Diproses</span>
                                        @elseif($mail->status == 'completed')
                                            <span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>Selesai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($mail->file)
                                            <span class="badge bg-success"><i class="bx bx-check me-1"></i>Tersedia</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bx bx-x me-1"></i>Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('mail-submissions.show', $mail->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            @if (Auth::user()->role == 'admin')
                                                <a href="{{ route('mail-submissions.edit', $mail->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $mail->id }}, '{{ $mail->name }} - {{ $mail->jenis_surat }}')" title="Hapus">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $mails->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bx bx-message-square-x" style="font-size: 4rem; color: #ddd;"></i>
                    <h5 class="mt-3 text-muted">Belum ada Pengajuan Surat</h5>
                    <p class="text-muted">Pengajuan Surat akan muncul di sini setelah ditambahkan</p>
                    @if (Auth::user()->role == 'admin')
                        <a href="{{ route('mail-submissions.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-2"></i>Tambah Pengajuan Surat Pertama
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id, title) {
        if (confirm(`Apakah Anda yakin ingin menghapus pengajuan surat "${title}"?`)) {
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
@endpush
