{{-- Status Filter Widget --}}
<div class="widget-sidber">
    <div class="widget-sidber-content">
        <h4>Filtrer par statut</h4>
    </div>
    <div class="filter-widget">
        <div class="filter-options">
            @php
                $statusFilters = [
                    ['data' => 'all', 'icon' => 'fas fa-list', 'label' => 'Tous les statuts', 'active' => true],
                    ['data' => 'upcoming', 'icon' => 'fas fa-clock', 'label' => 'À venir'],
                    ['data' => 'ongoing', 'icon' => 'fas fa-play-circle', 'label' => 'En cours'],
                    ['data' => 'completed', 'icon' => 'fas fa-check-circle', 'label' => 'Terminé'],
                    ['data' => 'cancelled', 'icon' => 'fas fa-times-circle', 'label' => 'Annulé']
                ];
            @endphp
            
            @foreach($statusFilters as $filter)
                <div class="filter-item status-filter-item {{ $filter['active'] ?? false ? 'active' : '' }}" data-status="{{ $filter['data'] }}">
                    <i class="{{ $filter['icon'] }}"></i>
                    <span>{{ $filter['label'] }}</span>
                    <i class="{{ $filter['active'] ?? false ? 'bi bi-check-circle-fill' : 'bi bi-circle' }} status-indicator"></i>
                </div>
            @endforeach
        </div>
    </div>
</div>