// Global Table Functionality
window.globalTableConfig = window.globalTableConfig || {};

function initializeGlobalTable(tableId) {
    const config = {
        tableId: tableId,
        perPage: parseInt(document.querySelector(`#${tableId}_tbody`)?.dataset?.perPage || 10),
        currentPage: 1,
        totalPages: 1,
        filteredData: [],
        allData: [],
        searchableColumns: JSON.parse(document.querySelector(`#${tableId}_tbody`)?.dataset?.searchableColumns || '[]'),
        filterColumns: {},
        url: document.querySelector(`#${tableId}_tbody`)?.dataset?.url || null
    };

    // Store config globally
    window.globalTableConfig[tableId] = config;
    
    setupTableEventListeners(tableId);
    
    // If URL is provided, load data from server
    if (config.url) {
        loadTableData(tableId);
    } else {
        // Client-side table
        setupClientSideTable(tableId);
    }
}

function setupTableEventListeners(tableId) {
    const searchInput = document.getElementById(`${tableId}_search`);
    const clearSearchBtn = document.getElementById(`${tableId}_clearSearch`);
    const clearFiltersBtn = document.getElementById(`${tableId}_clearFilters`);
    
    // Search event
    if (searchInput) {
        searchInput.addEventListener('input', () => performTableSearch(tableId));
        searchInput.addEventListener('keyup', () => performTableSearch(tableId));
    }
    
    // Clear search
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => clearTableSearch(tableId));
    }
    
    // Filter events
    const filterSelects = document.querySelectorAll(`#${tableId} select[data-filter]`);
    filterSelects.forEach(select => {
        select.addEventListener('change', () => performTableFilter(tableId));
    });
    
    // Clear filters
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', () => clearTableFilters(tableId));
    }
}

function setupClientSideTable(tableId) {
    const config = window.globalTableConfig[tableId];
    const tbody = document.getElementById(`${tableId}_tbody`);
    
    if (!tbody) return;
    
    // Extract data from existing table rows
    const rows = Array.from(tbody.querySelectorAll('tr'));
    config.allData = rows.map(row => ({
        element: row,
        searchData: extractRowSearchData(row),
        filterData: extractRowFilterData(row)
    }));
    
    config.filteredData = [...config.allData];
    updateTablePagination(tableId);
}

function extractRowSearchData(row) {
    const config = window.globalTableConfig[Object.keys(window.globalTableConfig)[0]];
    const data = {};
    
    if (config.searchableColumns) {
        config.searchableColumns.forEach(column => {
            const cell = row.querySelector(`[data-search="${column}"]`);
            if (cell) {
                data[column] = cell.textContent.toLowerCase();
            }
        });
    }
    
    return data;
}

function extractRowFilterData(row) {
    const data = {};
    const elements = row.querySelectorAll('[data-filter]');
    
    elements.forEach(element => {
        const filterType = element.getAttribute('data-filter');
        if (filterType) {
            data[filterType] = element.textContent.toLowerCase();
        }
    });
    
    return data;
}

function performTableSearch(tableId) {
    const config = window.globalTableConfig[tableId];
    const searchInput = document.getElementById(`${tableId}_search`);
    if (!searchInput) return;
    
    const searchTerm = searchInput.value.toLowerCase().trim();
    config.currentPage = 1;
    
    if (config.url) {
        // Server-side search
        loadTableData(tableId);
    } else {
        // Client-side search
        performClientSideSearch(tableId, searchTerm);
    }
}

function performClientSideSearch(tableId, searchTerm) {
    const config = window.globalTableConfig[tableId];
    
    if (!searchTerm) {
        config.filteredData = [...config.allData];
    } else {
        config.filteredData = config.allData.filter(item => {
            const matchesSearch = Object.values(item.searchData).some(value => 
                value.includes(searchTerm)
            );
            const matchesFilters = matchesActiveFilters(item, tableId);
            return matchesSearch && matchesFilters;
        });
    }
    
    updateTableDisplay(tableId);
    updateTablePagination(tableId);
}

function performTableFilter(tableId) {
    const config = window.globalTableConfig[tableId];
    config.currentPage = 1;
    
    if (config.url) {
        loadTableData(tableId);
    } else {
        performClientSideFilter(tableId);
    }
}

function performClientSideFilter(tableId) {
    const config = window.globalTableConfig[tableId];
    
    config.filteredData = config.allData.filter(item => {
        const matchesSearch = matchesSearchTerm(item, tableId);
        const matchesFilters = matchesActiveFilters(item, tableId);
        return matchesSearch && matchesFilters;
    });
    
    updateTableDisplay(tableId);
    updateTablePagination(tableId);
}

function matchesSearchTerm(item, tableId) {
    const config = window.globalTableConfig[tableId];
    const searchInput = document.getElementById(`${tableId}_search`);
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
    
    if (!searchTerm) return true;
    
    return Object.values(item.searchData).some(value => 
        value.includes(searchTerm)
    );
}

function matchesActiveFilters(item, tableId) {
    const filterSelects = document.querySelectorAll(`#${tableId} select[data-filter]`);
    
    for (let select of filterSelects) {
        const filterValue = select.value;
        const filterType = select.getAttribute('data-filter');
        
        if (filterValue && item.filterData[filterType] && 
            !item.filterData[filterType].includes(filterValue.toLowerCase())) {
            return false;
        }
    }
    
    return true;
}

function updateTableDisplay(tableId) {
    const config = window.globalTableConfig[tableId];
    const tbody = document.getElementById(`${tableId}_tbody`);
    
    if (!tbody) return;
    
    // Hide all rows first
    const allRows = Array.from(tbody.querySelectorAll('tr'));
    allRows.forEach(row => {
        row.style.display = 'none';
        row.classList.add('table-row-hidden');
    });
    
    // Calculate pagination range
    const startIndex = (config.currentPage - 1) * config.perPage;
    const endIndex = startIndex + config.perPage;
    const visibleItems = config.filteredData.slice(startIndex, endIndex);
    
    // Show visible rows
    visibleItems.forEach(item => {
        item.element.style.display = '';
        item.element.classList.remove('table-row-hidden');
    });
    
    // Update clear search button visibility
    const searchInput = document.getElementById(`${tableId}_search`);
    const clearSearchBtn = document.getElementById(`${tableId}_clearSearch`);
    
    if (clearSearchBtn && searchInput) {
        clearSearchBtn.style.display = searchInput.value.trim() ? 'inline-block' : 'none';
    }
    
    // Show/hide no results message
    toggleNoResultsMessage(tableId, config.filteredData.length === 0);
}

function updateTablePagination(tableId) {
    const config = window.globalTableConfig[tableId];
    const paginationContainer = document.getElementById(`${tableId}_pagination`);
    
    if (!paginationContainer || !config.url) {
        // Client-side pagination
        config.totalPages = Math.ceil(config.filteredData.length / config.perPage);
        generateClientPagination(tableId);
    }
}

function generateClientPagination(tableId) {
    const config = window.globalTableConfig[tableId];
    const paginationContainer = document.getElementById(`${tableId}_pagination`);
    
    if (!paginationContainer || config.totalPages <= 1) {
        if (paginationContainer) {
            paginationContainer.innerHTML = '';
        }
        return;
    }
    
    let paginationHtml = '<nav class="flex items-center justify-between"><div class="flex items-center space-x-2">';
    
    // Previous button
    if (config.currentPage > 1) {
        paginationHtml += `<button onclick="changePage('${tableId}', ${config.currentPage - 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Previous</button>`;
    }
    
    // Page numbers
    const startPage = Math.max(1, config.currentPage - 2);
    const endPage = Math.min(config.totalPages, config.currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const isActive = i === config.currentPage;
        paginationHtml += `<button onclick="changePage('${tableId}', ${i})" class="px-3 py-2 text-sm font-medium ${isActive ? 'text-white bg-blue-600' : 'text-gray-500 bg-white hover:bg-gray-50'} border border-gray-300 rounded-md">${i}</button>`;
    }
    
    // Next button
    if (config.currentPage < config.totalPages) {
        paginationHtml += `<button onclick="changePage('${tableId}', ${config.currentPage + 1})" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Next</button>`;
    }
    
    paginationHtml += '</div>';
    paginationHtml += `<div class="text-sm text-gray-500">Showing ${Math.min((config.currentPage - 1) * config.perPage + 1, config.filteredData.length)} to ${Math.min(config.currentPage * config.perPage, config.filteredData.length)} of ${config.filteredData.length} results</div>`;
    paginationHtml += '</nav>';
    
    paginationContainer.innerHTML = paginationHtml;
}

function changePage(tableId, page) {
    const config = window.globalTableConfig[tableId];
    config.currentPage = page;
    updateTableDisplay(tableId);
}

function clearTableSearch(tableId) {
    const searchInput = document.getElementById(`${tableId}_search`);
    if (searchInput) {
        searchInput.value = '';
        performTableSearch(tableId);
        searchInput.focus();
    }
}

function clearTableFilters(tableId) {
    const filterSelects = document.querySelectorAll(`#${tableId} select[data-filter]`);
    filterSelects.forEach(select => {
        select.value = '';
    });
    performTableFilter(tableId);
}

function loadTableData(tableId) {
    const config = window.globalTableConfig[tableId];
    const params = new URLSearchParams();
    
    // Add search param
    const searchInput = document.getElementById(`#${tableId}_search`);
    if (searchInput?.value) {
        params.append('search', searchInput.value);
    }
    
    // Add filter params
    const filterSelects = document.querySelectorAll(`#${tableId} select[data-filter]`);
    
    if (filterSelects) {
        for (let i = 0; i < filterSelects.length; i++) {
            if (filterSelects[i].value) {
                params.append(filterSelects[i].name, filterSelects[i].value);
            }
        }
    }
    
    params.append('page', config.currentPage);
    params.append('per_page', config.perPage);
    
    fetch(`${config.url}?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success !== false) {
                updateTableWithData(tableId, data);
            }
        })
        .catch(error => {
            console.error('Error loading table data:', error);
        });
}

function updateTableWithData(tableId, data) {
    // This would be implemented based on the specific table structure
    // For now, it's a placeholder for server-side data loading
    console.log('Updating table with data:', data);
}

function toggleNoResultsMessage(tableId, show) {
    const tbody = document.getElementById(`${tableId}_tbody`);
    if (!tbody) return;
    
    let noResultsRow = document.getElementById(`noResultsRow`);
    
    if (show && !noResultsRow) {
        noResultsRow = document.createElement('tr');
        noResultsRow.id = 'noResultsRow';
        
        const columnsCount = tbody.previousElementSibling.querySelectorAll('th').length;
        noResultsRow.innerHTML = `
            <td colspan="${columnsCount}" class="px-6 py-12 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-search text-4xl mb-4"></i>
                    <p class="text-lg font-medium">No results found</p>
                    <p class="text-sm">Try adjusting your search or filter criteria</p>
                </div>
            </td>
        `;
        
        tbody.appendChild(noResultsRow);
    } else if (!show && noResultsRow) {
        noResultsRow.remove();
    }
}

// Initialize all tables on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-initialize tables with data-global-table attribute
    document.querySelectorAll('[data-global-table]').forEach(table => {
        initializeGlobalTable(table.getAttribute('data-global-table'));
    });
});
