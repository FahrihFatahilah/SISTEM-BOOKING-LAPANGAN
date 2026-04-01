@extends('admin.layouts.app')

@section('title', 'Member Aktif')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Member Aktif</h2>
                <p class="text-muted">Kelola membership pelanggan</p>
            </div>
            <a href="{{ route('admin.user-memberships.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Member
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Paket</th>
                        <th>Lapangan</th>
                        <th>Jadwal</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($memberships as $membership)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ $membership->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $membership->user->email }}</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $membership->membershipPackage->name }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $membership->membershipPackage->sessions_per_week }}x/minggu, 
                                    {{ $membership->membershipPackage->session_duration_hours }} jam
                                </small>
                            </div>
                        </td>
                        <td>{{ $membership->field->name }}</td>
                        <td>
                            <div>
                                @php
                                    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    $scheduleDays = collect($membership->weekly_schedule)->map(fn($day) => $days[$day])->join(', ');
                                @endphp
                                <strong>{{ $scheduleDays }}</strong>
                                <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($membership->start_time)->format('H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <small>{{ $membership->start_date->format('d/m/Y') }} - {{ $membership->end_date->format('d/m/Y') }}</small>
                                <br>
                                @if($membership->end_date < now())
                                    <span class="badge bg-danger">Expired</span>
                                @elseif($membership->end_date->diffInDays(now()) <= 7)
                                    <span class="badge bg-warning">Akan Berakhir</span>
                                @else
                                    <span class="badge bg-success">{{ $membership->end_date->diffInDays(now()) }} hari lagi</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($membership->status === 'active')
                                <span class="badge bg-success">Aktif</span>
                            @elseif($membership->status === 'expired')
                                <span class="badge bg-danger">Expired</span>
                            @else
                                <span class="badge bg-secondary">Dibatalkan</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.user-memberships.show', $membership) }}">
                                        <i class="bi bi-eye me-2"></i>Detail
                                    </a></li>
                                    @if($membership->status === 'active')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.user-memberships.destroy', $membership) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Yakin ingin membatalkan membership ini?')">
                                                <i class="bi bi-x-circle me-2"></i>Batalkan
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">Belum ada member</h5>
                            <p class="text-muted">Silakan tambah member baru untuk memulai</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($memberships->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $memberships->links() }}
</div>
@endif
@endsection