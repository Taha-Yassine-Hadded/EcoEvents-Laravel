{{-- Event Card Component --}}
<div class="col-lg-6 col-md-6" data-category="{{ $event->category->name ?? '' }}" data-status="{{ $event->status }}" data-event-date="{{ $event->date }}">
    <div class="single-blog-box">
        {{-- Event Image with Status Badge --}}
        <div class="single-blog-thumb">
            @if ($event->img && Storage::disk('public')->exists($event->img))
                <img src="{{ url('/') }}/storage/{{ $event->img }}" alt="{{ $event->title }}">
            @else
                <img src="{{ asset('storage/events/default-event.jpg') }}" alt="{{ $event->title }}">
            @endif
            
            <div class="event-status-badge status-{{ $event->status }}">
                @switch($event->status)
                    @case('upcoming') À venir @break
                    @case('ongoing') En cours @break
                    @case('completed') Terminé @break
                    @default Annulé
                @endswitch
            </div>
        </div>
        
        {{-- Event Content --}}
        <div class="blog-content">
            <a href="{{ route('front.events.show', $event->id) }}">{{ $event->title }}</a>
            <p>{{ Str::limit($event->description ?? 'Aucune description disponible.', 45) }}</p>
            
            {{-- Event Meta Information --}}
            <div class="event-meta">
                <div class="event-category">
                    <i class="fas fa-leaf"></i>
                    <span>{{ $event->category->name ?? 'Non catégorisé' }}</span>
                </div>
                <div class="event-date">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ $event->date ? $event->date->format('d M Y à H:i') : 'Date non définie' }}</span>
                </div>
            </div>
        </div>
        
        {{-- Event Author/Organizer --}}
        <div class="blog-arthor">
            <div class="blog-author-title">
                <h6>
                    <span>{{ strtoupper(substr($event->organizer?->name ?? 'O', 0, 1)) }}</span>
                    {{ $event->organizer?->name ?? 'Organisateur' }}
                </h6>
            </div>
            <div class="blog-button">
                <a href="{{ route('front.events.show', $event->id) }}"><i class="bi bi-arrow-right-short"></i></a>
            </div>
        </div>
    </div>
</div>