@extends('backend.admin.layouts.app')

@section('title', 'Detail Berita - Portal Parakan')

@section('page-header')
  <div class="d-flex justify-content-between align-items-center">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Portal Parakan / Berita /</span> Detail Berita
    </h4>
    <div class="d-flex gap-2">
      {{-- <a href="{{ route('news.edit', $news->id) }}" class="btn btn-warning">
        <i class="bx bx-edit me-1"></i> Edit
      </a> --}}
      <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back me-1"></i> Kembali
      </a>
    </div>
  </div>
@endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="{{ route('news.index') }}">Berita</a></li>
  <li class="breadcrumb-item active">Detail Berita</li>
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
              @if($news->status == 'published')
                <span class="badge bg-success status-badge">
                  <i class="bx bx-check-circle me-1"></i>Dipublikasikan
                </span>
              @else
                <span class="badge bg-warning status-badge">
                  Draft
                </span>
              @endif
            </div>
            <div class="text-end">
              <small class="text-muted">{{ $news->created_at }}</small>
            </div>
          </div>
          
          <h1 class="h3 mb-3">{{ $news->title }}</h1>
          
          @if($news->category)
            <div class="mb-3">
              <span class="badge bg-label-primary">
                <i class="bx bx-tag me-1"></i>{{ $news->category }}
              </span>
            </div>
          @endif

          <!-- Author Info -->
          <div class="d-flex align-items-center mb-3">
            <div class="avatar avatar-sm me-3">
              <span class="avatar-initial rounded-circle bg-label-primary">
                {{ substr($news->user->name ?? 'A', 0, 1) }}
              </span>
            </div>
            <div>
              <h6 class="mb-0">{{ $news->user->name ?? 'Anonymous' }}</h6>
              <small class="text-muted">Penulis</small>
            </div>
          </div>

          <!-- Featured Image -->
          @if($news->image)
            <div class="mb-4">
              <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" 
                   class="news-image" width="100%" onclick="showImageModal('{{ asset('storage/' . $news->image) }}', '{{ $news->title }}')">
            </div>
          @endif

          <!-- Content -->
          <div class="news-content">
            {!! nl2br(e($news->content)) !!}
          </div>

          <!-- Tags -->
          @if($news->tags)
            <div class="mt-4 pt-4 border-top">
              <h6 class="mb-2">Tags:</h6>
              <div class="d-flex flex-wrap gap-2">
                @foreach(explode(',', $news->tags) as $tag)
                  <span class="badge bg-label-secondary">{{ trim($tag) }}</span>
                @endforeach
              </div>
            </div>
          @endif
        </div>
      </div>

      <!-- Comments Section (if implemented) -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bx bx-comment me-2"></i>Komentar
          </h5>
        </div>
        <div class="card-body">
          <div class="text-center py-4">
            <i class="bx bx-comment bx-lg text-muted mb-3"></i>
            <p class="text-muted">Fitur komentar akan segera tersedia</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <!-- Statistics -->
      <div class="card news-stats mb-4">
        <div class="card-body">
          <h6 class="text-white mb-3">
            <i class="bx bx-bar-chart-alt me-2"></i>Statistik Berita
          </h6>
          <div class="row text-center">
            <div class="col-6">
              <div class="mb-2">
                <h4 class="text-white mb-0">{{ $news->likes ?? 0 }}</h4>
                <small class="text-white-50">Likes</small>
              </div>
            </div>
            <div class="col-6">
              <div class="mb-2">
                <h4 class="text-white mb-0">{{ $news->views ?? 0 }}</h4>
                <small class="text-white-50">Views</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- News Info -->
      <div class="card mb-4">
        <div class="card-header">
          <h6 class="mb-0">
            <i class="bx bx-info-circle me-2"></i>Informasi Berita
          </h6>
        </div>
        <div class="card-body">
          <div class="news-meta p-3 mb-3">
            <div class="row">
              <div class="col-sm-4">
                <small class="text-muted">ID Berita:</small>
              </div>
              <div class="col-sm-8">
                <small class="fw-semibold">#{{ $news->id }}</small>
              </div>
            </div>
            <hr class="my-2">
            <div class="row">
              <div class="col-sm-4">
                <small class="text-muted">Slug:</small>
              </div>
              <div class="col-sm-8">
                <small class="fw-semibold">{{ $news->slug ?? 'Tidak ada' }}</small>
              </div>
            </div>
            <hr class="my-2">
            <div class="row">
              <div class="col-sm-4">
                <small class="text-muted">Dibuat:</small>
              </div>
              <div class="col-sm-8">
                <small class="fw-semibold">{{ $news->created_at}}</small>
              </div>
            </div>
            <hr class="my-2">
            <div class="row">
              <div class="col-sm-4">
                <small class="text-muted">Diperbarui:</small>
              </div>
              <div class="col-sm-8">
                <small class="fw-semibold">{{ $news->updated_at}}</small>
              </div>
            </div>
            @if($news->published_at)
              <hr class="my-2">
              <div class="row">
                <div class="col-sm-4">
                  <small class="text-muted">Dipublikasi:</small>
                </div>
                <div class="col-sm-8">
                  <small class="fw-semibold">{{ $news->published_at }}</small>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Actions -->
      @if (Auth::user()->role == 'admin')
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">
              <i class="bx bx-cog me-2"></i>Aksi
            </h6>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="{{ route('news.edit', $news->id) }}" class="btn btn-warning">
                <i class="bx bx-edit me-2"></i>Edit Berita
              </a>
              
              {{-- @if($news->status == 'draft')
                <form action="{{ route('news.update', $news->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="status" value="published">
                  <button type="submit" class="btn btn-success w-100">
                    <i class="bx bx-check me-2"></i>Publikasikan
                  </button>
                </form>
              @else
                <form action="{{ route('news.update', $news->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="status" value="draft">
                  <button type="submit" class="btn btn-outline-secondary w-100">
                    <i class="bx bx-edit me-2"></i>Jadikan Draft
                  </button>
                </form>
              @endif --}}
              
              <hr>
              
              <button type="button" class="btn btn-outline-danger" 
                      onclick="confirmDelete({{ $news->id }}, '{{ $news->title }}')">
                <i class="bx bx-trash me-2"></i>Hapus Berita
              </button>
            </div>
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
          <h5 class="modal-title" id="imageModalTitle">Gambar Berita</h5>
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
        `Apakah Anda yakin ingin menghapus berita "${title}"? Tindakan ini tidak dapat dibatalkan.`,
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
