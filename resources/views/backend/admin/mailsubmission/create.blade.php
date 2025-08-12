@extends('backend.admin.layouts.app')

@section('title', 'Tambah Pengajuan Surat - Portal Parakan')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Portal Parakan / Pengajuan Surat /</span> Tambah Pengajuan Surat
        </h4>
        <a href="{{ route('mail-submissions.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>
@endsection

@push('styles')
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .drag-drop-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .drag-drop-area:hover,
        .drag-drop-area.dragover {
            border-color: #696cff;
            background-color: #f8f9ff;
        }

        .drag-drop-area.dragover {
            transform: scale(1.02);
        }
    </style>
@endpush

@section('content')
    <form action="{{ route('mail-submissions.store') }}" method="POST" enctype="multipart/form-data" id="mailsForm">
        @csrf
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-edit me-2"></i>Informasi Pengajuan Surat</h5>
                    </div>
                    <div class="card-body">
                        <!-- Dropdown Jenis Surat -->
                        <div class="mb-3">
                            <label for="jenis_surat" class="form-label">Jenis Keperluan Surat</label>
                            <select name="jenis_surat" id="jenis_surat" class="form-select">
                                <option disabled selected>Pilih jenis surat...</option>
                                <option value="tidak_mampu">Surat Keterangan Tidak Mampu</option>
                                <option value="usaha">Surat Keterangan Usaha</option>
                                <option value="domisili">Surat Keterangan Domisili</option>
                                <option value="belum_menikah">Surat Keterangan Belum Menikah</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control"
                                    value="{{ old('tempat_lahir') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control"
                                    value="{{ old('tanggal_lahir') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="" disabled selected>Pilih...</option>
                                <option value="Laki-laki"
                                    {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan"
                                    {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <!-- Form Dinamis -->
                        <div id="form-dinamis">
                            <!-- Tidak Mampu -->
                            <div class="form-jenis" id="form-tidak_mampu" style="display:none;">

                                <div class="mb-3">
                                    <label>Nama Sekolah</label>
                                    <input type="text" name="nama_sekolah" class="form-control"
                                        value="{{ old('nama_sekolah') }}">
                                </div>

                                <div class="mb-3">
                                    <label>Nama Orang Tua</label>
                                    <input type="text" name="nama_ortu" class="form-control"
                                        value="{{ old('nama_ortu') }}">
                                </div>

                                <div class="mb-3">
                                    <label>Pekerjaan Orang Tua</label>
                                    <input type="text" name="pekerjaan_ortu" class="form-control"
                                        value="{{ old('pekerjaan_ortu') }}">
                                </div>

                            </div>

                            <!-- Usaha -->
                            <div class="form-jenis" id="form-usaha" style="display:none;">
                                {{-- <div class="mb-3">
                                    <label>Nama</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" class="form-control"
                                            value="{{ old('tempat_lahir') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" class="form-control"
                                            value="{{ old('tanggal_lahir') }}">
                                    </div>
                                </div> --}}

                                <div class="mb-3">
                                    <label>Pekerjaan</label>
                                    <input type="text" name="pekerjaan" class="form-control"
                                        value="{{ old('pekerjaan') }}">
                                </div>

                                {{-- <div class="mb-3">
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control">{{ old('alamat') }}</textarea>
                                </div> --}}

                                <div class="mb-3">
                                    <label>Jenis Usaha</label>
                                    <input type="text" name="jenis_usaha" class="form-control"
                                        value="{{ old('jenis_usaha') }}">
                                </div>
                            </div>


                            <!-- Domisili -->
                            <div class="form-jenis" id="form-domisili" style="display:none;">
                                {{-- <div class="mb-3">
                                    <label>Nama</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name') }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" class="form-control"
                                            value="{{ old('tempat_lahir') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" class="form-control"
                                            value="{{ old('tanggal_lahir') }}">
                                    </div>
                                </div> --}}

                                <div class="mb-3">
                                    <label>NIK</label>
                                    <input type="number" name="nik" class="form-control"
                                        value="{{ old('nik') }}">
                                </div>

                                {{-- <div class="mb-3">
                                    <label>Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-select">
                                        <option value="">Pilih...</option>
                                        <option value="Laki-laki"
                                            {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan"
                                            {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div> --}}

                                <div class="mb-3">
                                    <label>Alamat KTP</label>
                                    <textarea name="alamat_ktp" class="form-control">{{ old('alamat_ktp') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label>Alamat Domisili</label>
                                    <textarea name="alamat_domisili" class="form-control">{{ old('alamat_domisili') }}</textarea>
                                </div>
                            </div>


                            <!-- Belum Menikah -->
                            <div class="form-jenis" id="form-belum_menikah" style="display:none;">
                                {{-- <div class="mb-3">
                                    <label>Nama</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name') }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" class="form-control"
                                            value="{{ old('tempat_lahir') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" class="form-control"
                                            value="{{ old('tanggal_lahir') }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-select">
                                        <option value="">Pilih...</option>
                                        <option value="Laki-laki"
                                            {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan"
                                            {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div> --}}

                                {{-- <div class="mb-3">
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control">{{ old('alamat') }}</textarea>
                                </div> --}}
                            </div>

                        </div>

                        <div class="mb-3">
                            <label>Alamat Saat Ini</label>
                            <textarea name="alamat" class="form-control">{{ old('alamat') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Featured Image -->
                <div class="card mb-4">
                    <div class="card-body">
                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description	" class="form-label">Deskripsi Pengajuan Surat <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Publish Settings -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-cog me-2"></i>Aksi</h5>
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <input type="hidden" name="status" value="draft" id="status">

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Pengajuan Surat
                            </button>
                            <a href="{{ route('mail-submissions.index') }}" class="btn btn-outline-danger">
                                <i class="bx bx-x me-1"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-id-ID.min.js"></script>

    <script>
        $(document).ready(function() {
            function showForm(jenis) {
                $('.form-jenis').hide();
                if (!jenis) return;
                $('#form-' + jenis).show();
            }

            $('#jenis_surat').on('change', function() {
                showForm($(this).val());
            });

            // tampilkan sesuai old() / nilai awal saat load
            showForm($('#jenis_surat').val());
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize Summernote
            $('#description').summernote({
                height: 120,
                lang: 'id-ID',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']]
                ]
            });

            // Auto-generate slug from title
            $('#title').on('input', function() {
                const title = $(this).val();
                const slug = title.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
                $('#slug').val(slug);
            });

            // Character counter for excerpt
            $('#excerpt').on('input', function() {
                const maxLength = 300;
                const currentLength = $(this).val().length;
                const remaining = maxLength - currentLength;

                // Update or create counter
                let counter = $('#excerpt').siblings('.char-counter');
                if (counter.length === 0) {
                    counter = $('<div class="char-counter form-text"></div>');
                    $('#excerpt').after(counter);
                }

                counter.text(`${currentLength}/${maxLength} karakter`);
                counter.toggleClass('text-danger', remaining < 0);
            });
        });


        // Form validation before submit
        document.getElementById('mailsForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = $('#description').summernote('code').trim();

            if (!title) {
                e.preventDefault();
                alert('Judul Pengajuan Surat harus diisi!');
                document.getElementById('title').focus();
                return;
            }

            if (!description || description === '<p><br></p>') {
                e.preventDefault();
                alert('Deskripsi Pengajuan Surat harus diisi!');
                $('#description').summernote('focus');
                return;
            }

            // ⬇ Tambahkan baris ini
            $('#description').val(description); // Sinkronkan ke textarea

            showLoading();
        });

        // Auto-save draft every 2 minutes
        let autoSaveInterval;

        function startAutoSave() {
            autoSaveInterval = setInterval(function() {
                const title = document.getElementById('title').value.trim();
                const description = $('#description').summernote('code').trim();

                if (title && description && description !== '<p><br></p>') {
                    // Implement auto-save logic here
                    console.log('Auto-saving draft...');
                }
            }, 120000); // 2 minutes
        }

        // Start auto-save when user starts typing
        let typingTimer;
        document.getElementById('title').addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(startAutoSave, 3000);
        });
    </script>
@endpush
