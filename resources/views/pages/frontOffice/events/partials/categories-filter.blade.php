{{-- Categories Filter Widget --}}
<div class="widget-sidber">
    <div class="widget-sidber-content">
        <h4>Catégories</h4>
    </div>
    <div class="widget-category">
        <ul id="categoryFilter">
            @forelse($categories as $category)
                <li>
                    <a href="#" class="category-filter-item" data-category="{{ $category->name }}">
                        <div class="category-content">
                            <i class="fas fa-leaf category-icon"></i>
                            <span class="category-name">{{ $category->name }}</span>
                            <span class="category-count">({{ $category->events_count ?? 0 }})</span>
                        </div>
                    </a>
                </li>
            @empty
                <li><span class="text-muted">Aucune catégorie disponible</span></li>
            @endforelse
        </ul>
        <div class="category-actions mt-3">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearCategoryFilters()">
                Effacer les filtres
            </button>
        </div>
    </div>
</div>