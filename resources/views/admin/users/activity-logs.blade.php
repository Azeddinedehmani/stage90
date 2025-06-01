@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-history me-2"></i>Logs d'activité - {{ $user->name }}</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></li>
                <li class="breadcrumb-item active">Activités</li>
            </ol>
        </nav>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group">
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                <i class="fas fa-user me-1"></i> Voir profil
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>
</div>

<!-- Profil utilisateur compact -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                @if($user->profile_photo)
                    <img src="{{ asset('storage/'.$user->profile_photo) }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle" 
                         style="width: 80px; height: 80px; object-fit: cover;">
                @else
                    <div class="bg-{{ $user->role === 'responsable' ? 'info' : 'success' }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                         style="width: 80px; height: 80px;">
                        <i class="fas {{ $user->role === 'responsable' ? 'fa-user-shield' : 'fa-user-md' }} fa-2x"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-1">{{ $user->email }}</p>
                <div class="d-flex gap-2">
                    <span class="badge bg-{{ $user->role === 'responsable' ? 'info' : 'success' }}">
                        {{ $user->role === 'responsable' ? 'Responsable' : 'Pharmacien' }}
                    </span>
                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row text-center">
                    <div class="col-4">
                        <h5 class="text-primary mb-0">{{ $activities->total() }}</h5>
                        <small class="text-muted">Total activités</small>
                    </div>
                    <div class="col-4">
                        <h5 class="text-success mb-0">{{ $user->activityLogs()->where('action', 'login')->count() }}</h5>
                        <small class="text-muted">Connexions</small>
                    </div>
                    <div class="col-4">
                        <h5 class="text-info mb-0">{{ $user->activityLogs()->whereDate('created_at', today())->count() }}</h5>
                        <small class="text-muted">Aujourd'hui</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">
            <i class="fas fa-filter me-2"></i>Filtrer les activités
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.activity-logs', $user->id) }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="action" class="form-label">Type d'action</label>
                <select class="form-select" id="action" name="action">
                    <option value="">Toutes les actions</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Connexion</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Déconnexion</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Création</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Modification</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Suppression</option>
                    <option value="view" {{ request('action') == 'view' ? 'selected' : '' }}>Consultation</option>
                    <option value="export" {{ request('action') == 'export' ? 'selected' : '' }}>Export</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">Date de début</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Date de fin</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.users.activity-logs', $user->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Timeline des activités -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-clock me-2"></i>Chronologie des activités
            @if(request()->hasAny(['action', 'date_from', 'date_to']))
                <span class="badge bg-info ms-2">Filtré</span>
            @endif
        </h5>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-outline-primary" onclick="exportActivities()">
                <i class="fas fa-download me-1"></i> Exporter
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($activities->count() > 0)
            <div class="timeline">
                @foreach($activities as $activity)
                    <div class="timeline-item mb-4">
                        <div class="row">
                            <div class="col-md-2 text-center">
                                <div class="timeline-date">
                                    <div class="fw-bold">{{ $activity->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $activity->created_at->format('H:i:s') }}</div>
                                    <div class="text-success small">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                                <div class="timeline-badge bg-{{ $this->getActionColor($activity->action) }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                     style="width: 40px; height: 40px;">
                                    <i class="{{ $activity->action_icon }}"></i>
                                </div>
                                @if(!$loop->last)
                                    <div class="timeline-line bg-light" style="width: 2px; height: 60px; margin: 10px auto;"></div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <div class="timeline-content bg-light rounded p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge {{ $activity->action_badge }} me-2">
                                                {{ ucfirst($activity->action) }}
                                            </span>
                                            @if($activity->model_type)
                                                <span class="badge bg-secondary">{{ $activity->model_name }}</span>
                                            @endif
                                        </div>
                                        @if($activity->ip_address)
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <code>{{ $activity->ip_address }}</code>
                                            </small>
                                        @endif
                                    </div>
                                    
                                    <div class="activity-description">
                                        <strong>{{ $activity->description }}</strong>
                                    </div>
                                    
                                    @if($activity->old_values || $activity->new_values)
                                        <div class="mt-3">
                                            <button class="btn btn-sm btn-outline-info" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#details{{ $activity->id }}">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Voir les détails
                                            </button>
                                        </div>
                                        
                                        <div class="collapse mt-3" id="details{{ $activity->id }}">
                                            <div class="row">
                                                @if($activity->old_values)
                                                    <div class="col-md-6">
                                                        <h6 class="text-danger">
                                                            <i class="fas fa-minus-circle me-1"></i>
                                                            Anciennes valeurs
                                                        </h6>
                                                        <div class="bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded p-2">
                                                            <pre class="mb-0 small">{{ json_encode($activity->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if($activity->new_values)
                                                    <div class="col-md-6">
                                                        <h6 class="text-success">
                                                            <i class="fas fa-plus-circle me-1"></i>
                                                            Nouvelles valeurs
                                                        </h6>
                                                        <div class="bg-success bg-opacity-10 border border-success border-opacity-25 rounded p-2">
                                                            <pre class="mb-0 small">{{ json_encode($activity->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($activity->user_agent)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-globe me-1"></i>
                                                <strong>Navigateur:</strong> {{ Str::limit($activity->user_agent, 80) }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-history fa-3x mb-3"></i>
                    <h5>Aucune activité trouvée</h5>
                    @if(request()->hasAny(['action', 'date_from', 'date_to']))
                        <p class="mb-0">Aucune activité ne correspond à vos critères de filtrage.</p>
                        <a href="{{ route('admin.users.activity-logs', $user->id) }}" class="btn btn-outline-primary mt-3">
                            <i class="fas fa-eye me-1"></i> Voir toutes les activités
                        </a>
                    @else
                        <p class="mb-0">Cet utilisateur n'a encore aucune activité enregistrée.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <!-- Pagination -->
    @if($activities->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Affichage de {{ $activities->firstItem() }} à {{ $activities->lastItem() }} sur {{ $activities->total() }} activités
                </div>
                {{ $activities->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

@section('scripts')
<script>
    function exportActivities() {
        // Construction de l'URL d'export avec les filtres actuels
        const params = new URLSearchParams(window.location.search);
        params.set('export', '1');
        
        const exportUrl = `{{ route('admin.users.activity-logs', $user->id) }}?${params.toString()}`;
        window.location.href = exportUrl;
    }

    // Auto-refresh toutes les 60 secondes si on regarde les activités récentes
    @if(!request()->hasAny(['date_from', 'date_to']) || (request('date_to') && request('date_to') == today()->format('Y-m-d')))
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        }, 60000);
    @endif
</script>

<style>
    .timeline-item {
        position: relative;
    }
    
    .timeline-content {
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .timeline-badge {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .activity-description {
        font-size: 0.95rem;
        line-height: 1.5;
    }
</style>

@php
function getActionColor($action) {
    return match($action) {
        'create' => 'success',
        'update' => 'warning',
        'delete' => 'danger',
        'view' => 'info',
        'login' => 'primary',
        'logout' => 'secondary',
        'export' => 'dark',
        default => 'light'
    };
}
@endphp

@endsection