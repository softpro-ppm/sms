@props([
    'id' => 'globalTable',
    'data' => [],
    'columns' => [],
    'filters' => [],
    'searchableColumns' => [],
    'perPage' => 10,
    'title' => 'Data Table',
    'url' => null,
    'emptyMessage' => 'No data found',
    'showSearch' => true,
    'showFilters' => true,
    'showPagination' => true
])

<div class="bg-white rounded-xl shadow-lg overflow-hidden" id="{{ $id }}">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
    </div>

    @if($showSearch || $showFilters)
    <!-- Search and Filters -->
    <div class="bg-white border-b border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            @if($showSearch)
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input type="text" 
                           id="{{ $id }}_search"
                           placeholder="Search by {{ implode(', ', $searchableColumns) }}..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-80">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <button id="{{ $id }}_clearSearch" 
                        class="px-4 py-2 text-gray-500 hover:text-gray-700 transition-colors duration-200"
                        style="display: none;">
                    <i class="fas fa-times mr-1"></i>
                    Clear
                </button>
            </div>
            @endif

            @if($showFilters && !blank($filters))
            <div class="flex items-center space-x-4">
                @foreach($filters as $filter)
                <select id="{{ $id }}_{{ array_keys($filter)[0] }}" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        data-filter="{{ array_keys($filter)[0] }}">
                    <option value="">{{ array_values($filter)[0] }}</option>
                    @if(isset($filter['options']))
                        @foreach($filter['options'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    @endif
                </select>
                @endforeach
                <button id="{{ $id }}_clearFilters" 
                        class="px-4 py-2 text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-refresh mr-1"></i>
                    Reset
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($columns as $column)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ isset($column['class']) ? $column['class'] : '' }}">
                        {{ $column['label'] }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="{{ $id }}_tbody">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if($showPagination)
    <!-- Pagination -->
    <div id="{{ $id }}_pagination" class="px-6 py-4 border-t border-gray-200">
        {{ $pagination ?? '' }}
    </div>
    @endif
</div>

<!-- Global Table Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeGlobalTable('{{ $id }}');
});
</script>
