@extends('layout.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="row">
        <!-- Informasi Profil -->
        <div class="col-lg-8">
            <!-- Card Informasi Personal -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-user me-2"></i>Informasi Personal
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Informasi Akun -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-user-shield me-2"></i>Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Role</label>
                                <p class="mb-0">
                                    <span class="badge bg-info fs-6">
                                        {{ $user->role->name ?? 'Tidak ada role' }}
                                    </span>
                                </p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Status</label>
                                <p class="mb-0">
                                    @if($user->status == 'Active')
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check-circle me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-times-circle me-1"></i>Non-Aktif
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">ID Pengguna</label>
                                <p class="mb-0 fs-6">{{ $user->user_id }}</p>
                            </div>
                            <div class="info-item mb-3">
                                <label class="fw-bold text-muted small">Terdaftar Sejak</label>
                                <p class="mb-0 fs-6">{{ $user->created_at->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Aktivitas Login -->
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-history me-2"></i>Aktivitas Terakhir
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
                                            <i class="fas fa-sign-in-alt me-1"></i>
                                            {{ $user->last_login->format('d F Y H:i') }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            ({{ $user->last_login->diffForHumans() }})
                                        </small>
                                    @else
                                        <span class="text-muted">Belum tercatat</span>
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
                                            <i class="fas fa-sign-out-alt me-1"></i>
                                            {{ $user->last_logout->format('d F Y H:i') }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            ({{ $user->last_logout->diffForHumans() }})
                                        </small>
                                    @else
                                        <span class="text-muted">Belum tercatat</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Card Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-edit me-2"></i>Edit Profil
                        </a>

                        <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal"
                            data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </button>

                        @if($user->last_login && $user->last_login->diffInDays() > 30)
                            <div class="alert alert-warning">
                                <small>
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Anda belum login selama {{ $user->last_login->diffInDays() }} hari
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card Statistik -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistik
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 text-primary mb-1">
                                    {{ number_format($user->created_at->diffInDays()) }}
                                </div>
                                <small class="text-muted">Hari Bergabung</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success mb-1">
                                {{ $user->last_login ? number_format($user->last_login->diffInDays()) : '0' }}
                            </div>
                            <small class="text-muted">Hari sejak login</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Informasi Keamanan -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-shield-alt me-2"></i>Keamanan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="security-status">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <div>
                                <small class="fw-bold">Profil Terverifikasi</small>
                                <br>
                                <small class="text-muted">Email: {{ $user->email }}</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-clock text-warning me-2"></i>
                            <div>
                                <small class="fw-bold">Terakhir Update</small>
                                <br>
                                <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        <div class="alert alert-light border">
                            <small>
                                <i class="fas fa-info-circle me-1 text-info"></i>
                                Pastikan informasi profil Anda selalu diperbarui untuk keamanan akun.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-light col-12">Back</a>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('profile.change-password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Ubah Password</button>
                    </div>
                </form>
            </div>
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

        .security-status .fas {
            font-size: 1.2em;
        }
    </style>
@endsection