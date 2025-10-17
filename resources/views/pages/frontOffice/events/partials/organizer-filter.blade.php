{{-- Organizer Filter Widget --}}
<div class="widget-sidber">
    <div class="widget-sidber-content">
        <h4>Événements</h4>
    </div>
    <div class="filter-widget">
        <div class="filter-options">
            @php
                $organizerFilters = [
                    ['data' => 'all', 'icon' => 'fas fa-globe', 'label' => 'Tous les événements', 'active' => request('organizer_filter') !== 'mine'],
                    ['data' => 'mine', 'icon' => 'fas fa-user', 'label' => 'Mes événements', 'active' => request('organizer_filter') === 'mine']
                ];
            @endphp
            
            @foreach($organizerFilters as $filter)
                <div class="filter-item organizer-filter-item {{ $filter['active'] ? 'active' : '' }}" 
                     data-organizer="{{ $filter['data'] }}" 
                     onclick="filterOrganizerEvents('{{ $filter['data'] }}')"
                     id="{{ $filter['data'] === 'all' ? 'all-events-btn' : 'my-events-btn' }}">
                    <i class="{{ $filter['icon'] }}"></i>
                    <span>{{ $filter['label'] }}</span>
                    <i class="{{ $filter['active'] ? 'bi bi-check-circle-fill' : 'bi bi-circle' }} status-indicator"></i>
                </div>
            @endforeach
        </div>
    </div>
</div>