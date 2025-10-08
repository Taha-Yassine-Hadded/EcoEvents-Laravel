{{-- Time Filter Widget --}}
<div class="widget-sidber">
    <div class="widget-sidber-content">
        <h4>Filtrer par période</h4>
    </div>
    <div class="filter-widget">
        <div class="filter-options">
            @php
                $timeFilters = [
                    ['data' => 'all', 'icon' => 'fas fa-calendar', 'label' => 'Tous les événements', 'active' => true],
                    ['data' => 'today', 'icon' => 'fas fa-calendar-day', 'label' => 'Aujourd\'hui'],
                    ['data' => 'week', 'icon' => 'fas fa-calendar-week', 'label' => 'Cette semaine'],
                    ['data' => 'month', 'icon' => 'fas fa-calendar-alt', 'label' => 'Ce mois-ci']
                ];
            @endphp
            
            @foreach($timeFilters as $filter)
                <div class="filter-item time-filter-item {{ $filter['active'] ?? false ? 'active' : '' }}" data-time="{{ $filter['data'] }}">
                    <i class="{{ $filter['icon'] }}"></i>
                    <span>{{ $filter['label'] }}</span>
                    <i class="{{ $filter['active'] ?? false ? 'bi bi-check-circle-fill' : 'bi bi-circle' }} time-indicator"></i>
                </div>
            @endforeach
        </div>
    </div>
</div>