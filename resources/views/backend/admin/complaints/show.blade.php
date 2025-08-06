@extends('backend.admin.layouts.app')

@section('title', 'Detail Pengaduan - Portal Parakan')

@section('page-header')
  <div class="d-flex justify-content-between align-items-center">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Portal Parakan / Pengaduan /</span> Detail Pengaduan
    </h4>
    <div class="d-flex gap-2">
      @if (Auth::user()->role == 'admin')
        <a href="{{ route('complaint-response.create', ['complaint_id' => $complaint->id]) }}" class="btn btn-info">
          <i class="bx bx-pencil me-1"></i> Tanggapi
        </a>
      @endif
      <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back me-1"></i> Kembali
      </a>
    </div>
  </div>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="{{ route('complaints.index') }}">Pengaduan</a></li>
  <li class="breadcrumb-item active">Detail Pengaduan</li>
@endsection

@push('styles')
  <style>
    .news-image {
      max-width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 8px;
    }
    .news-content {
      line-height: 1.6;
    }
    .news-meta {
      border-left: 4px solid #696cff;
      padding-left: 1rem;
      background-color: #f8f9ff;
    }
    .status-badge {
      font-size: 0.875rem;
      padding: 0.5rem 1rem;
    }
    .news-stats {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 12px;
    }
  </style>
@endpush

@section('content')
  <div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
      <!-- News Header -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              @if($complaint->status == 'resolved')
                <span class="badge bg-success status-badge">
                  <i class="bx bx-check-circle me-1"></i>Selesai
                </span>
              @elseif($complaint->status == 'rejected')
                <span class="badge bg-warning status-badge">
                  <i class="bx bx-x-circle me-1"></i>Ditolak
                </span>
              @else
                <span class="badge bg-warning status-badge">
                  <i class="bx bx-archive me-1"></i> Draft
                </span>
              @endif
            </div>
            <div class="text-end">
              <small class="text-muted">{{ $complaint->created_at }}</small>
            </div>
          </div>
          
          <h1 class="h3 mb-3">{{ $complaint->title }}</h1>
          
          @if($complaint->category)
            <div class="mb-3">
              <span class="badge bg-label-primary">
                <i class="bx bx-tag me-1"></i>{{ $complaint->category }}
              </span>
            </div>
          @endif

          <!-- Author Info -->
          <div class="d-flex align-items-center mb-3">
            <div class="avatar avatar-sm me-3">
              <span class="avatar-initial rounded-circle bg-label-primary">
                {{ substr($complaint->user->name ?? 'A', 0, 1) }}
              </span>
            </div>
            <div>
              <h6 class="mb-0">{{ $complaint->user->name ?? 'Anonymous' }}</h6>
              <small class="text-muted">Penulis</small>
            </div>
          </div>

          <!-- Featured Image -->
          @if($complaint->image)
            <div class="mb-4">
              <img src="{{ asset('storage/' . $complaint->image) }}" alt="{{ $complaint->title }}" 
                   class="news-image" width="100%" onclick="showImageModal('{{ asset('storage/' . $complaint->image) }}', '{{ $complaint->title }}')">
            </div>
          @endif

          <!-- Description -->
          <div class="news-content">
            {!! $complaint->description !!}
          </div>

        </div>
      </div>

    </div>

    <div class="col-lg-4">
      <!-- News Info -->
      <div class="card mb-4">
        <div class="card-header">
          <h6 class="mb-0">
            <i class="bx bx-info-circle me-2"></i>Informasi Pengaduan
          </h6>
        </div>
        <div class="card-body">
          <div class="news-meta p-3 mb-3">
            <div class="row">
              <div class="col-sm-4">
                <small class="text-muted">ID Pengaduan:</small>
              </div>
              <div class="col-sm-8">
                <small class="fw-semibold">#{{ $complaint->id }}</small>
              </div>
            </div>
            <hr class="my-2">
            <div class="row">
              <div class="col-sm-4">
                <small class="text-muted">Dibuat:</small>
              </div>
              <div class="col-sm-8">
                <small class="fw-semibold">{{ $complaint->created_at}}</small>
              </div>
            </div>
            <hr class="my-2">
            <div class="row">
              <div class="col-sm-4">
                <small class="text-muted">Diperbarui:</small>
              </div>
              <div class="col-sm-8">
                <small class="fw-semibold">{{ $complaint->updated_at}}</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="card mb-4">
        <div class="card-header">
          <h6 class="mb-0">
            <i class="bx bx-cog me-2"></i>Aksi
          </h6>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="{{ route('complaints.edit', $complaint->id) }}" class="btn btn-warning">
              <i class="bx bx-edit me-2"></i>Edit Pengaduan
            </a>
            
            {{-- @if($complaint->status == 'draft')
              <form action="{{ route('complaints.update', $complaint->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="resolved">
                <button type="submit" class="btn btn-success w-100">
                  <i class="bx bx-check me-2"></i>Selesai
                </button>
              </form>
            @else
              <form action="{{ route('complaints.update', $complaint->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="draft">
                <button type="submit" class="btn btn-outline-secondary w-100">
                  <i class="bx bx-archive me-2"></i>Jadikan Draft
                </button>
              </form>
            @endif --}}
            
            <hr>
            
            <button type="button" class="btn btn-outline-danger" 
                    onclick="confirmDelete({{ $complaint->id }}, '{{ $complaint->title }}')">
              <i class="bx bx-trash me-2"></i>Hapus Pengaduan
            </button>
          </div>
        </div>
      </div>

      <!-- Responses -->
      @if (Auth::user()->role == 'admin')
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">
              <i class="bx bx-message-square-detail me-2"></i>Tanggapan Pengaduan
            </h6>
          </div>
          <div class="card-body">
            @if($complaint->responses && $complaint->responses->count() > 0)
              <div class="mb-3">
                <small class="text-muted">{{ $complaint->responses->count() }} tanggapan ditemukan</small>
              </div>
              
              @foreach($complaint->responses->take(3) as $response)
                <div class="border-start border-primary border-3 ps-3 mb-3">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                      <h6 class="mb-1">
                        Tanggapan #{{ $response->id }}
                        @if($response->status == 'pending')
                          <span class="badge bg-warning ms-2">Pending</span>
                        @elseif($response->status == 'process')
                          <span class="badge bg-info ms-2">Diproses</span>
                        @elseif($response->status == 'resolved')
                          <span class="badge bg-success ms-2">Selesai</span>
                        @endif
                      </h6>
                      <small class="text-muted">{{ $response->user->name ?? 'Admin' }} • {{ $response->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    <a href="{{ route('complaint-response.show', $response->id) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bx bx-show"></i>
                    </a>
                  </div>
                  <p class="mb-0 small">{!! Str::limit($response->response, 150) !!}</p>
                </div>
              @endforeach
              
              @if($complaint->responses->count() > 3)
                <div class="text-center">
                  <small class="text-muted">dan {{ $complaint->responses->count() - 3 }} tanggapan lainnya</small>
                </div>
              @endif
              
              <hr>
              
              <div class="d-grid gap-2">
                <a href="{{ route('complaint-response.create', ['complaint_id' => $complaint->id]) }}" class="btn btn-primary">
                  <i class="bx bx-plus me-2"></i>Tambah Tanggapan
                </a>
                
                @if($complaint->responses->count() > 0)
                  <a href="{{ route('complaint-response.index', ['complaint_id' => $complaint->id]) }}" class="btn btn-outline-secondary">
                    <i class="bx bx-list-ul me-2"></i>Lihat Semua Tanggapan
                  </a>
                @endif
              </div>
            @else
              <div class="text-center py-3">
                <i class="bx bx-message-square-x" style="font-size: 2rem; color: #ddd;"></i>
                <h6 class="mt-2 text-muted">Belum ada tanggapan</h6>
                <p class="text-muted small mb-3">Pengaduan ini belum memiliki tanggapan</p>
                <a href="{{ route('complaint-response.create', ['complaint_id' => $complaint->id]) }}" class="btn btn-primary">
                  <i class="bx bx-plus me-2"></i>Buat Tanggapan Pertama
                </a>
              </div>
            @endif
          </div>
        </div>
      @endif
    </div>
  </div>


  <!-- Image Modal -->
  <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="imageModalTitle">Gambar Pengaduan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img id="modalImage" src="" alt="" class="img-fluid rounded">
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    function showImageModal(imageSrc, title) {
      document.getElementById('modalImage').src = imageSrc;
      document.getElementById('imageModalTitle').textContent = title;
      const modal = new bootstrap.Modal(document.getElementById('imageModal'));
      modal.show();
    }

    function confirmDelete(id, title) {
      showConfirmModal(
        `Apakah Anda yakin ingin menghapus Pengaduan "${title}"? Tindakan ini tidak dapat dibatalkan.`,
        function() {
          // Create form and submit
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = `/news/${id}`;
          
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
