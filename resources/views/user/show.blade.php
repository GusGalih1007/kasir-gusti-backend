@extends('layout.app')

@section('title', 'Detail User - ' . $user->username)

@section('content')

    <div class="row">
        <!-- Informasi Utama -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-user-circle me-2"></i>Informasi Profil
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Username</label>
                                <p class="mb-0 fs-5">{{ $user->username }}</p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Nama Lengkap</label>
                                <p class="mb-0 fs-6">{{ $user->first_name }} {{ $user->last_name }}</p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Role</label>
                                <p class="mb-0">
                                    <span class="badge bg-info fs-6">
                                        {{ $user->role->name ?? 'Tidak ada role' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Email</label>
                                <p class="mb-0 fs-6">
                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                        {{ $user->email }}
                                    </a>
                                </p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Telepon</label>
                                <p class="mb-0 fs-6">
                                    @if($user->phone)
                                        <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                            {{ $user->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Status</label>
                                <p class="mb-0">
                                    @if($user->status == 'Active')
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check-circle me-1"></i>Aktif
                                        </span>
                                    @elseif($user->status == 'inactive')
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-times-circle me-1"></i>Non-Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary fs-6">{{ $user->status }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Login -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-sign-in-alt me-2"></i>Informasi Login
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Login Terakhir</label>
                                <p class="mb-0 fs-6">
                                    @if($user->last_login)
                                        <span class="text-success">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $user->last_login->format('d F Y H:i') }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            ({{ $user->last_login->diffForHumans() }})
                                        </small>
                                    @else
                                        <span class="text-muted">Belum pernah login</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Logout Terakhir</label>
                                <p class="mb-0 fs-6">
                                    @if($user->last_logout)
                                        <span class="text-warning">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $user->last_logout->format('d F Y H:i') }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            ({{ $user->last_logout->diffForHumans() }})
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Sistem -->
            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-database me-2"></i>Informasi Sistem
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Dibuat Oleh</label>
                                <p class="mb-0 fs-6">{{ $user->userCreator->username ?? 'System' }}</p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Tanggal Dibuat</label>
                                <p class="mb-0 fs-6">{{ $user->created_at->format('d F Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Diupdate Oleh</label>
                                <p class="mb-0 fs-6">{{ $user->userUpdator->username ?? '-' }}</p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Terakhir Update</label>
                                <p class="mb-0 fs-6">{{ $user->updated_at->format('d F Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status & Actions -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-user-shield me-2"></i>Status Akun
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($user->status == 'Active')
                            <i class="fas fa-user-check fa-3x text-success mb-3"></i>
                            <div class="fw-bold text-success">AKTIF</div>
                        @else
                            <i class="fas fa-user-slash fa-3x text-danger mb-3"></i>
                            <div class="fw-bold text-danger">NON-AKTIF</div>
                        @endif
                    </div>
                    @if (App\Helpers\PermissionHelper::hasPermission('update'))
                        @if($user->status == 'Active')
                            <form action="{{ route('user.deactivate', $user->user_id) }}" method="POST" class="d-grid">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning btn-lg"
                                    onclick="return confirm('Non-aktifkan user ini?')">
                                    <i class="fas fa-user-slash me-2"></i>Non-Aktifkan
                                </button>
                            </form>
                        @else
                            <form action="{{ route('user.activate', $user->user_id) }}" method="POST" class="d-grid">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Aktifkan user ini?')">
                                    <i class="fas fa-user-check me-2"></i>Aktifkan
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if (App\Helpers\PermissionHelper::hasPermission('update') || App\Helpers\PermissionHelper::hasPermission('delete'))
                <div class="card mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-4 text-white">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if (App\Helpers\PermissionHelper::hasPermission('update'))
                                <a href="{{ route('user.edit', $user->user_id) }}" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-edit me-2"></i>Edit Profil
                                </a>
                            @endif

                            @if (App\Helpers\PermissionHelper::hasPermission('delete'))
                                @if(auth()->id() != $user->user_id)
                                    <form action="{{ route('user.destroy', $user->user_id) }}" method="POST" class="d-grid">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-lg"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan!')">
                                            <i class="fas fa-trash me-2"></i>Hapus User
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-outline-secondary btn-lg" disabled>
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Akun Sendiri
                                    </button>
                                    <small class="text-muted text-center">
                                        Tidak dapat menghapus akun sendiri
                                    </small>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Informasi Keamanan -->
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-shield-alt me-2"></i>Informasi Keamanan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border">
                        <small>
                            <i class="fas fa-info-circle me-1 text-info"></i>
                            <strong>Terakhir aktif:</strong>
                            @if($user->last_login)
                                {{ $user->last_login->diffForHumans() }}
                            @else
                                Belum pernah login
                            @endif
                        </small>
                    </div>

                    @if($user->updated_at->diffInDays() > 30)
                        <div class="alert alert-warning">
                            <small>
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Profil belum diupdate dalam {{ $user->updated_at->diffInDays() }} hari
                            </small>
                        </div>
                    @endif
                </div>
            </div>
            <a href="{{ route('user.index') }}" class="btn btn-light col-12">Back</a>
        </div>
    </div>

    <style>
        .info-item {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 0.75rem;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        .btn-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }
    </style>
@endsection
