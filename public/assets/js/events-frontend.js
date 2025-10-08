/**
 * Events Frontend JavaScript - Optimized
 * Handles pagination, filtering, and search functionality
 */

class EventsManager {
    constructor() {
        this.currentPage = 1;
        this.eventsPerPage = 8;
        this.filteredEvents = [];
        this.allEventBoxes = [];
        this.selectedCategories = [];
        this.selectedTimeFilter = 'all';
        this.selectedStatusFilter = 'all';
        this.debounceTimer = null;
        
        this.init();
    }
    
    init() {
        this.cacheElements();
        this.bindEvents();
        this.initializeFilters();
        this.updatePagination();
        this.showPage(1);
    }
    
    cacheElements() {
        this.searchInput = document.getElementById('searchInput');
        this.allEventBoxes = Array.from(document.querySelectorAll('.col-lg-6:has(.single-blog-box)'));
        this.filteredEvents = [...this.allEventBoxes];
        this.paginationNumbers = document.getElementById('paginationNumbers');
        this.paginationContainer = document.querySelector('.custom-pagination');
        this.prevBtn = document.querySelector('.prev-btn');
        this.nextBtn = document.querySelector('.next-btn');
    }
    
    bindEvents() {
        // Search functionality with debouncing
        this.searchInput?.addEventListener('input', () => {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.applyFilters(), 300);
        });
        
        // Category filter events
        document.querySelectorAll('.category-filter-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const categoryName = item.getAttribute('data-category');
                this.toggleCategoryFilter(categoryName);
            });
        });
        
        // Time filter events
        document.querySelectorAll('.time-filter-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const timeFilter = item.getAttribute('data-time');
                this.handleFilterToggle(timeFilter, 'time');
            });
        });
        
        // Status filter events
        document.querySelectorAll('.status-filter-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const statusFilter = item.getAttribute('data-status');
                this.handleFilterToggle(statusFilter, 'status');
            });
        });
    }
    
    initializeFilters() {
        // Set default active states (already set in HTML)
        this.updateFilterUI('time', 'all');
        this.updateFilterUI('status', 'all');
    }
    
    // Unified filter toggle handler
    handleFilterToggle(filterValue, filterType) {
        const isCurrentlyActive = filterType === 'time' ? 
            this.selectedTimeFilter === filterValue : 
            this.selectedStatusFilter === filterValue;
            
        if (isCurrentlyActive && filterValue !== 'all') {
            // Deselect - go back to 'all'
            this.setFilter(filterType, 'all');
        } else {
            // Select new filter
            this.setFilter(filterType, filterValue);
        }
    }
    
    setFilter(filterType, filterValue) {
        if (filterType === 'time') {
            this.selectedTimeFilter = filterValue;
        } else if (filterType === 'status') {
            this.selectedStatusFilter = filterValue;
        }
        
        this.updateFilterUI(filterType, filterValue);
        this.applyFilters();
    }
    
    updateFilterUI(filterType, activeValue) {
        const selector = filterType === 'time' ? '.time-filter-item' : '.status-filter-item';
        const indicatorClass = filterType === 'time' ? '.time-indicator' : '.status-indicator';
        
        document.querySelectorAll(selector).forEach(item => {
            const itemValue = item.getAttribute(`data-${filterType}`);
            const indicator = item.querySelector(indicatorClass);
            
            if (itemValue === activeValue) {
                item.classList.add('active');
                indicator.classList.remove('bi-circle');
                indicator.classList.add('bi-check-circle-fill');
            } else {
                item.classList.remove('active');
                indicator.classList.remove('bi-check-circle-fill');
                indicator.classList.add('bi-circle');
            }
        });
    }
    
    toggleCategoryFilter(categoryName) {
        const index = this.selectedCategories.indexOf(categoryName);
        if (index > -1) {
            this.selectedCategories.splice(index, 1);
        } else {
            this.selectedCategories.push(categoryName);
        }
        
        this.updateCategoryFilterUI();
        this.applyFilters();
    }
    
    updateCategoryFilterUI() {
        document.querySelectorAll('.category-filter-item').forEach(item => {
            const categoryName = item.getAttribute('data-category');
            item.classList.toggle('active', this.selectedCategories.includes(categoryName));
        });
    }
    
    clearCategoryFilters() {
        this.selectedCategories = [];
        this.updateCategoryFilterUI();
        this.applyFilters();
    }
    
    applyFilters() {
        const searchTerm = this.searchInput?.value.toLowerCase() || '';
        
        this.filteredEvents = this.allEventBoxes.filter(eventCol => {
            const eventBox = eventCol.querySelector('.single-blog-box');
            if (!eventBox) return false;
            
            // Extract text content safely
            const title = eventBox.querySelector('.blog-content a')?.textContent.toLowerCase() || '';
            const description = eventBox.querySelector('.blog-content p')?.textContent.toLowerCase() || '';
            const author = eventBox.querySelector('.blog-author-title h6')?.textContent.toLowerCase() || '';
            
            // Apply all filters
            const matchesSearch = this.matchesSearchFilter(searchTerm, title, description, author);
            const matchesCategory = this.matchesCategoryFilter(eventCol);
            const matchesTime = this.matchesTimeFilter(eventCol);
            const matchesStatus = this.matchesStatusFilter(eventCol);
            
            return matchesSearch && matchesCategory && matchesTime && matchesStatus;
        });
        
        this.currentPage = 1;
        this.updatePagination();
        this.showPage(1);
    }
    
    matchesSearchFilter(searchTerm, title, description, author) {
        return !searchTerm || [title, description, author].some(text => text.includes(searchTerm));
    }
    
    matchesCategoryFilter(eventCol) {
        if (this.selectedCategories.length === 0) return true;
        const eventCategory = eventCol.getAttribute('data-category');
        return this.selectedCategories.includes(eventCategory);
    }
    
    matchesTimeFilter(eventCol) {
        if (this.selectedTimeFilter === 'all') return true;
        
        const eventDate = eventCol.getAttribute('data-event-date');
        if (!eventDate) return false;
        
        return this.checkTimeFilter(eventDate, this.selectedTimeFilter);
    }
    
    matchesStatusFilter(eventCol) {
        if (this.selectedStatusFilter === 'all') return true;
        const eventStatus = eventCol.getAttribute('data-status');
        return eventStatus === this.selectedStatusFilter;
    }
    
    checkTimeFilter(eventDate, timeFilter) {
        const eventDateTime = new Date(eventDate);
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const eventDay = new Date(eventDateTime.getFullYear(), eventDateTime.getMonth(), eventDateTime.getDate());
        
        switch(timeFilter) {
            case 'today':
                return eventDay.getTime() === today.getTime();
                
            case 'week':
                const weekStart = new Date(today);
                weekStart.setDate(today.getDate() - today.getDay());
                const weekEnd = new Date(weekStart);
                weekEnd.setDate(weekStart.getDate() + 6);
                return eventDay >= weekStart && eventDay <= weekEnd;
                
            case 'month':
                const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                return eventDay >= monthStart && eventDay <= monthEnd;
                
            default:
                return true;
        }
    }
    
    showPage(page) {
        this.currentPage = page;
        const startIndex = (page - 1) * this.eventsPerPage;
        const endIndex = startIndex + this.eventsPerPage;
        
        // Hide all events
        this.allEventBoxes.forEach(eventCol => {
            eventCol.style.display = 'none';
        });
        
        // Show events for current page
        const eventsToShow = this.filteredEvents.slice(startIndex, endIndex);
        eventsToShow.forEach(eventCol => {
            eventCol.style.display = 'block';
        });
        
        this.updatePaginationButtons();
        this.scrollToTop();
    }
    
    updatePagination() {
        const totalPages = Math.ceil(this.filteredEvents.length / this.eventsPerPage);
        
        if (totalPages <= 1) {
            this.paginationContainer.style.display = 'none';
            return;
        } else {
            this.paginationContainer.style.display = 'flex';
        }
        
        this.paginationNumbers.innerHTML = this.generatePageNumbers(totalPages);
    }
    
    generatePageNumbers(totalPages) {
        let html = '';
        
        for (let i = 1; i <= totalPages; i++) {
            const shouldShow = i === 1 || i === totalPages || 
                             (i >= this.currentPage - 1 && i <= this.currentPage + 1);
            
            if (shouldShow) {
                const isActive = i === this.currentPage ? 'active' : '';
                html += `<div class="page-number ${isActive}" onclick="eventsManager.showPage(${i})">${i}</div>`;
            } else if (i === this.currentPage - 2 || i === this.currentPage + 2) {
                html += '<div class="page-ellipsis">...</div>';
            }
        }
        
        return html;
    }
    
    updatePaginationButtons() {
        const totalPages = Math.ceil(this.filteredEvents.length / this.eventsPerPage);
        
        this.prevBtn.disabled = this.currentPage === 1;
        this.nextBtn.disabled = this.currentPage === totalPages;
        
        // Update page number active states
        document.querySelectorAll('.page-number').forEach(btn => {
            const pageNum = parseInt(btn.textContent);
            btn.classList.toggle('active', pageNum === this.currentPage);
        });
    }
    
    prevPage() {
        if (this.currentPage > 1) {
            this.showPage(this.currentPage - 1);
        }
    }
    
    nextPage() {
        const totalPages = Math.ceil(this.filteredEvents.length / this.eventsPerPage);
        if (this.currentPage < totalPages) {
            this.showPage(this.currentPage + 1);
        }
    }
    
    scrollToTop() {
        const target = document.querySelector('.blog-grid-area');
        if (target) {
            target.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }
    }
}

// Initialize when DOM is ready
let eventsManager;

document.addEventListener('DOMContentLoaded', function() {
    eventsManager = new EventsManager();
});

// Global functions for onclick handlers
function prevPage() {
    eventsManager?.prevPage();
}

function nextPage() {
    eventsManager?.nextPage();
}

function clearCategoryFilters() {
    eventsManager?.clearCategoryFilters();
}

function filterOrganizerEvents(filterType) {
    // Update URL and reload
    const url = new URL(window.location);
    if (filterType === 'mine') {
        url.searchParams.set('organizer_filter', 'mine');
    } else {
        url.searchParams.delete('organizer_filter');
    }
    
    window.location.href = url.toString();
}