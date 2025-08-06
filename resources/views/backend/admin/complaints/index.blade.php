@extends('backend.admin.layouts.app')

@section('title', 'Kelola Pengaduan - Portal Parakan')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Portal Parakan / Pengaduan /</span> Semua Pengaduan
        </h4>
        <a href="{{ route('complaints.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Pengaduan
        </a>
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('complaints.index') }}">Pengaduan</a></li>
    <li class="breadcrumb-item active">Semua Pengaduan</li>
@endsection

@push('styles')
    <style>
        .complaints-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .complaints-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .complaints-status {
            font-size: 0.75rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        .btn-sm i {
            font-size: 0.875rem;
        }
    </style>
@endpush

@section('content')
    <!-- Filter and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('complaints.index') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Cari Pengaduan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                placeholder="Judul pengaduan...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>
                                Diselesaikan</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                Ditolak</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search me-1"></i> Filter
                            </button>
                        </div>
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
                            <span class="fw-semibold d-block mb-1">Total Pengaduan</span>
                            <h3 class="card-title mb-0">{{ $totalComplaints ?? 0 }}</h3>
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
                            <i class="bx bx-x-circle bx-md text-danger"></i>
                        </div>
                        <div>
                            <span class="fw-semibold d-block mb-1">Di Tolak</span>
                            <h3 class="card-title mb-0">{{ $rejectedComplaints ?? 0 }}</h3>
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
                            <span class="fw-semibold d-block mb-1">Draft</span>
                            <h3 class="card-title mb-0">{{ $draftComplaints ?? 0 }}</h3>
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
                            <h3 class="card-title mb-0">{{ $resolvedComplaints ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- complaints List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengaduan</h5>
        </div>
        <div class="card-body">
            @if (isset($complaints) && $complaints->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox"
                                        onchange="toggleSelectAll()">
                                </th>
                                <th>Nama Pengadu</th>
                                <th>Judul</th>
                                <th>Gambar</th>
                                <th>status</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($complaints as $item)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input complaints-checkbox"
                                            value="{{ $item->id }}" onchange="updateDeleteButton()">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($item->user->name ?? 'A', 0, 1) }}
                                                </span>
                                            </div>
                                            <span class="fw-semibold">{{ $item->user->name ?? 'Anonymous' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ Str::limit($item->title, 50) }}</h6>
                                            <small class="text-muted">{!! Str::limit($item->description, 80) !!}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($item->image)
                                            <img src="{{ asset('storage/' . $item->image) }}" alt="Complaints Image"
                                                class="rounded" width="60" height="40"
                                                style="object-fit: cover; cursor: pointer;"
                                                onclick="showImagePreview('{{ asset('storage/' . $item->image) }}', '{{ $item->title }}')">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                style="width: 60px; height: 40px;">
                                                <i class="bx bx-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->status == 'resolved')
                                            <span class="badge bg-success complaints-status">Selesai</span>
                                        @elseif ($item->status == 'rejected')
                                            <span class="badge bg-danger complaints-status">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning complaints-status">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $item->category ?? '' }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('complaints.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Lihat Detail">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            @if (Auth::user()->role == 'admin')
                                                <a href="{{ route('complaint-response.create', ['complaint_id' => $item->id]) }}"
                                                    class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Tanggapi Pengaduan">
                                                    <i class="bx bx-reply"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('complaints.edit', $item->id) }}"
                                                class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit Pengaduan">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Pengaduan"
                                                onclick="confirmDelete({{ $item->id }}, '{{ $item->title }}')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if (method_exists($complaints, 'links'))
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Menampilkan {{ $complaints->firstItem() }} - {{ $complaints->lastItem() }} dari
                                {{ $complaints->total() }}
                                pengaduan
                            </small>
                        </div>
                        <div>
                            {{ $complaints->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bx bx-complaints bx-lg text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Pengaduan</h5>
                    <p class="text-muted">Mulai tambahkan pengaduan pertama untuk portal desa Anda.</p>
                    <a href="{{ route('complaints.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah Pengaduan Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function confirmDelete(id, title) {
            showConfirmModal(
                `Apakah Anda yakin ingin menghapus berita "${title}"? Tindakan ini tidak dapat dibatalkan.`,
                function() {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/complaints/${id}`;

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    form.appendChild(methodInput);
                    form.appendChild(tokenInput);
                    document.body.appendChild(form);

                    showLoading();
                    form.submit();
                }
            );
        }
    </script>
@endpush
