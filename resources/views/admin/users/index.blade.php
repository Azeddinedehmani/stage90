@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-users me-2"></i>Gestion des utilisateurs</h2>
        <p class="text-muted">Administration complète des comptes utilisateurs et suivi d'activité</p>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nouvel utilisateur
            </a>
            <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.users.export') }}">
                    <i class="fas fa-download me-1"></i> Exporter la liste
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('admin.activity-logs') }}">
                    <i class="fas fa-history me-1"></i> Voir tous les logs
                </a></li>
            </ul>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        @if(session('temp_password'))
            <br><strong>Mot de passe temporaire: <code>{{ session('temp_password') }}</code></strong>
            <br><small class="text-muted">L'utilisateur devra le changer à sa prochaine connexion</small>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Statistiques Dashboard -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h4 class="mb-0">{{ $totalUsers }}</h4>
                <small>Total utilisateurs</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-user-check fa-2x mb-2"></i>
                <h4 class="mb-0">{{ $activeUsers }}</h4>
                <small>Actifs</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-user-shield fa-2x mb-2"></i>
                <h4 class="mb-0">{{ $adminUsers }}</h4>
                <small>Administrateurs</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <i class="fas fa-user-md fa-2x mb-2"></i>
                <h4 class="mb-0">{{ $pharmacistUsers }}</h4>
                <small>Pharmaciens</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <i class="fas fa-sign-in-alt fa-2x mb-2"></i>
                <h4 class="mb-0">{{ $recentLogins }}</h4>
                <small>Connexions 7j</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h4 class="mb-0">{{ $totalUsers - $activeUsers }}</h4>
                <small>Inactifs</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtres Avancés -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">
            <i class="fas fa-filter me-2"></i>Filtres et recherche
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Recherche globale</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Nom, email, téléphone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label for="role" class="form-label">Rôle</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Tous les rôles</option>
                    <option value="responsable" {{ request('role') == 'responsable' ? 'selected' : '' }}>Responsable</option>
                    <option value="pharmacien" {{ request('role') == 'pharmacien' ? 'selected' : '' }}>Pharmacien</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Statut</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="login_filter" class="form-label">Dernière connexion</label>
                <select class="form-select" id="login_filter" name="login_filter">
                    <option value="">Tous</option>
                    <option value="today" {{ request('login_filter') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="week" {{ request('login_filter') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="month" {{ request('login_filter') == 'month' ? 'selected' : '' }}>Ce mois</option>
                    <option value="never" {{ request('login_filter') == 'never' ? 'selected' : '' }}>Jamais connecté</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table des Utilisateurs -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-table me-2"></i>Liste des utilisateurs ({{ $users->total() }})
            @if(request()->hasAny(['search', 'role', 'status', 'login_filter']))
                <span class="badge bg-info ms-2">Filtré</span>
            @endif
        </h5>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-outline-secondary" onclick="toggleView('table')">
                <i class="fas fa-table"></i> Table
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="toggleView('cards')">
                <i class="fas fa-th-large"></i> Cartes
            </button>
        </div>
    </div>
    
    <!-- Vue Table -->
    <div id="table-view" class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 250px;">
                            <i class="fas fa-user me-1"></i>Utilisateur
                        </th>
                        <th style="width: 180px;">
                            <i class="fas fa-phone me-1"></i>Contact
                        </th>
                        <th style="width: 120px;">
                            <i class="fas fa-user-tag me-1"></i>Rôle
                        </th>
                        <th style="width: 150px;">
                            <i class="fas fa-clock me-1"></i>Dernière connexion
                        </th>
                        <th style="width: 100px;">
                            <i class="fas fa-chart-line me-1"></i>Activités
                        </th>
                        <th style="width: 100px;">
                            <i class="fas fa-toggle-on me-1"></i>Statut
                        </th>
                        <th style="width: 200px;">
                            <i class="fas fa-cogs me-1"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="{{ !$user->is_active ? 'table-secondary' : '' }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->profile_photo)
                                        <img src="{{ asset('storage/'.$user->profile_photo) }}" 
                                             alt="{{ $user->name }}" 
                                             class="rounded-circle me-3" 
                                             style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="bg-{{ $user->role === 'responsable' ? 'info' : 'success' }} text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 45px; height: 45px;">
                                            <i class="fas {{ $user->role === 'responsable' ? 'fa-user-shield' : 'fa-user-md' }}"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                        @if($user->force_password_change)
                                            <div class="text-warning small">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Changement mot de passe requis
                                            </div>
                                        @endif
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-primary small">Vous</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->phone)
                                    <div class="small">
                                        <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                                    </div>
                                @endif
                                @if($user->address)
                                    <div class="small text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($user->address, 25) }}
                                    </div>
                                @endif
                                @if(!$user->phone && !$user->address)
                                    <span class="text-muted small">Non renseigné</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->role === 'responsable' ? 'info' : 'success' }} fs-6">
                                    {{ $user->role === 'responsable' ? 'Responsable' : 'Pharmacien' }}
                                </span>
                                <div class="small text-muted mt-1">
                                    Membre depuis {{ $user->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td>
                                @if($user->last_login_at)
                                    <div class="small">
                                        <strong>{{ $user->last_login_at->format('d/m/Y') }}</strong>
                                    </div>
                                    <div class="small text-muted">
                                        {{ $user->last_login_at->format('H:i') }}
                                    </div>
                                    @if($user->last_login_ip)
                                        <div class="small text-muted">
                                            <code style="font-size: 0.75em;">{{ $user->last_login_ip }}</code>
                                        </div>
                                    @endif
                                    <div class="small text-success">
                                        {{ $user->last_login_at->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-warning small">
                                        <i class="fas fa-clock me-1"></i>Jamais connecté
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="fs-5">
                                    <span class="badge bg-primary">{{ $user->activity_logs_count }}</span>
                                </div>
                                <div class="small text-muted">activités</div>
                                @if($user->activity_logs_count > 0)
                                    <a href="{{ route('admin.users.activity-logs', $user->id) }}" 
                                       class="btn btn-outline-info btn-sm mt-1">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check-circle me-1"></i>Actif
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6">
                                        <i class="fas fa-pause-circle me-1"></i>Inactif
                                    </span>
                                @endif
                                
                                @if($user->force_password_change)
                                    <div class="mt-1">
                                        <span class="badge bg-warning text-dark small">
                                            <i class="fas fa-key me-1"></i>Reset requis
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <!-- Voir -->
                                    <a href="{{ route('admin.users.show', $user->id) }}" 
                                       class="btn btn-sm btn-info text-white" 
                                       title="Voir le profil">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Modifier -->
                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                       class="btn btn-sm btn-primary"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                        <!-- Toggle Status -->
                                        <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn btn-sm {{ $user->is_active ? 'btn-warning' : 'btn-success' }}"
                                                    title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir {{ $user->is_active ? 'désactiver' : 'activer' }} cet utilisateur ?')">
                                                <i class="fas {{ $user->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Reset Password -->
                                        <button type="button" 
                                                class="btn btn-sm btn-secondary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#resetPasswordModal{{ $user->id }}"
                                                title="Réinitialiser le mot de passe">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        
                                        <!-- Delete User -->
                                        @if($user->role !== 'responsable' || \App\Models\User::where('role', 'responsable')->where('id', '!=', $user->id)->count() > 0)
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $user->id }}"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    @else
                                        <span class="btn btn-sm btn-light disabled">Votre compte</span>
                                    @endif
                                </div>
                                
                                @if($user->id !== auth()->id())
                                    <!-- Reset Password Modal -->
                                    <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-key me-2"></i>
                                                        Réinitialiser le mot de passe
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="text-center mb-3">
                                                        @if($user->profile_photo)
                                                            <img src="{{ asset('storage/'.$user->profile_photo) }}" 
                                                                 alt="{{ $user->name }}" 
                                                                 class="rounded-circle" 
                                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                                                 style="width: 60px; height: 60px;">
                                                                <i class="fas fa-user fa-lg"></i>
                                                            </div>
                                                        @endif
                                                        <h6 class="mt-2">{{ $user->name }}</h6>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                    </div>
                                                    
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong>Action irréversible !</strong><br>
                                                        Un mot de passe temporaire sera généré et l'utilisateur devra le changer à sa prochaine connexion.
                                                    </div>
                                                    
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="confirmReset{{ $user->id }}" required>
                                                        <label class="form-check-label" for="confirmReset{{ $user->id }}">
                                                            Je confirme vouloir réinitialiser le mot de passe
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-1"></i>Annuler
                                                    </button>
                                                    <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-warning">
                                                            <i class="fas fa-key me-1"></i> Réinitialiser le mot de passe
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    @if($user->role !== 'responsable' || \App\Models\User::where('role', 'responsable')->where('id', '!=', $user->id)->count() > 0)
                                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-trash me-2"></i>
                                                            Supprimer l'utilisateur
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="text-center mb-3">
                                                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                                            <h5>Attention ! Action définitive</h5>
                                                        </div>
                                                        
                                                        <p>Êtes-vous sûr de vouloir supprimer <strong>{{ $user->name }}</strong> ?</p>
                                                        
                                                        <div class="alert alert-danger">
                                                            <strong>Cette action est irréversible !</strong><br>
                                                            Toutes les données associées à cet utilisateur seront conservées mais l'accès sera définitivement supprimé.
                                                        </div>
                                                        
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6><i class="fas fa-info-circle me-2"></i>Statistiques utilisateur :</h6>
                                                                <ul class="mb-0">
                                                                    <li>{{ $user->activity_logs_count }} activités enregistrées</li>
                                                                    <li>Membre depuis {{ $user->created_at->diffForHumans() }}</li>
                                                                    <li>Dernière connexion : {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Jamais' }}</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-check mt-3">
                                                            <input class="form-check-input" type="checkbox" id="confirmDelete{{ $user->id }}" required>
                                                            <label class="form-check-label" for="confirmDelete{{ $user->id }}">
                                                                Je comprends que cette action est définitive
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-1"></i>Annuler
                                                        </button>
                                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash me-1"></i> Supprimer définitivement
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <h5>Aucun utilisateur trouvé</h5>
                                    <p class="mb-0">Aucun utilisateur ne correspond à vos critères de recherche.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    @if($users->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Affichage de {{ $users->firstItem() }} à {{ $users->lastItem() }} sur {{ $users->total() }} utilisateurs
                </div>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

@section('scripts')
<script>
    function toggleView(view) {
        // Cette fonction peut être étendue pour alterner entre vue table et cartes
        console.log('Toggle view:', view);
    }

    // Auto-refresh des statistiques toutes les 30 secondes
    setInterval(function() {
        // Ici vous pouvez ajouter du code AJAX pour rafraîchir les statistiques
    }, 30000);

    // Recherche en temps réel (optionnel)
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            // Implémentation de la recherche en temps réel si désiré
        }, 500);
    });
</script>
@endsection
@endsection