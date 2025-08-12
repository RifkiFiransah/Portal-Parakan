@extends('backend.admin.layouts.app')

@section('title', 'Edit Pengajuan Surat | Portal Parakan')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Portal Parakan / Pengajuan Surat /</span> Edit Pengajuan
    </h4>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bx bx-edit me-2"></i>Edit Pengajuan Surat #{{ $mailSubmission->id }}</h5>
        <a href="{{ route('mail-submissions.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-2"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        {{-- Info Surat --}}
        <div class="alert alert-info">
            <i class="bx bx-info-circle me-2"></i>
            <strong>Jenis Surat:</strong> {{ $mailSubmission->jenis_surat }}<br>
            <small class="text-muted">Pemohon: {{ $mailSubmission->name }} (NIK: {{ $mailSubmission->nik }})</small>
        </div>

        <form action="{{ route('mail-submissions.update', $mailSubmission->id) }}" method="POST" enctype="multipart/form-data" id="editSubmissionForm">
            @csrf
            @method('PUT')

            {{-- Data Utama --}}
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">NIK <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('nik') is-invalid @enderror" name="nik" value="{{ old('nik', $mailSubmission->nik) }}" required>
                    @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. KK <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('no_kk') is-invalid @enderror" name="no_kk" value="{{ old('no_kk', $mailSubmission->no_kk) }}" required>
                    @error('no_kk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $mailSubmission->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. HP</label>
                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror" name="no_hp" value="{{ old('no_hp', $mailSubmission->no_hp) }}">
                    @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Pilih Jenis Surat --}}
            <div class="mt-3">
                <label class="form-label">Jenis Surat <span class="text-danger">*</span></label>
                <select class="form-select @error('jenis_surat') is-invalid @enderror" id="jenis_surat" name="jenis_surat" required>
                    <option value="">Pilih Jenis Surat</option>
                    @foreach($jenisSuratList as $surat)
                        <option value="{{ $surat }}" {{ old('jenis_surat', $mailSubmission->jenis_surat) == $surat ? 'selected' : '' }}>{{ $surat }}</option>
                    @endforeach
                </select>
                @error('jenis_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Field Dinamis --}}
            <div id="extraFields" class="mt-3">
                @include('backend.admin.mailsubmission.partials.fields', [
                    'jenis_surat' => old('jenis_surat', $mailSubmission->jenis_surat),
                    'data' => $mailSubmission
                ])
            </div>

            {{-- Status Surat --}}
            <div class="mt-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                    <option value="">Pilih Status</option>
                    <option value="pending" {{ old('status', $mailSubmission->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="process" {{ old('status', $mailSubmission->status) == 'process' ? 'selected' : '' }}>Diproses</option>
                    <option value="completed" {{ old('status', $mailSubmission->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- File Pendukung --}}
            <div class="mt-3">
                <label class="form-label">File Pendukung</label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($mailSubmission->file)
                    <small class="text-muted">File saat ini: <a href="{{ asset('storage/' . $mailSubmission->file) }}" target="_blank">{{ basename($mailSubmission->file) }}</a></small>
                @endif
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-2"></i>Simpan Perubahan</button>
                <a href="{{ route('mail-submissions.index') }}" class="btn btn-outline-secondary"><i class="bx bx-x me-2"></i>Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Load field dinamis saat ganti jenis_surat
    document.getElementById('jenis_surat').addEventListener('change', function() {
        const jenis = this.value;
        fetch(`/mail-submissions/fields/${jenis}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('extraFields').innerHTML = html;
            });
    });
</script>
@endsection
