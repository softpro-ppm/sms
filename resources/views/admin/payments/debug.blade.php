@extends('layouts.admin')

@section('title', 'Payments Debug')

@section('content')
<div class="p-6">
    <h1>Payments Debug Page</h1>
    
    <!-- Simple Search Test -->
    <div class="mb-4">
        <input type="text" id="testSearch" placeholder="Test search..." class="border p-2">
        <button id="testBtn">Test</button>
    </div>
    
    <!-- Simple Table -->
    <table class="border-collapse border w-full">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Student</th>
                <th class="border p-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr class="test-row" 
                data-student="{{ $payment->student ? strtolower($payment->student->full_name) : 'n/a' }}"
                data-status="{{ $payment->status }}">
                <td class="border p-2">{{ $payment->student ? $payment->student->full_name : 'N/A' }}</td>
                <td class="border p-2">{{ $payment->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div id="debug" class="mt-4 p-4 bg-gray-100"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testSearch = document.getElementById('testSearch');
    const testBtn = document.getElementById('testBtn');
    const testRows = document.querySelectorAll('.test-row');
    const debug = document.getElementById('debug');
    
    debug.innerHTML = 'JavaScript loaded. Found ' + testRows.length + ' rows.';
    
    function testSearchFunction() {
        const searchTerm = testSearch.value.toLowerCase();
        let visibleCount = 0;
        
        testRows.forEach(row => {
            const studentName = row.dataset.student || '';
            const matches = studentName.includes(searchTerm);
            
            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        debug.innerHTML = 'Search term: "' + searchTerm + '", Visible rows: ' + visibleCount;
    }
    
    if (testSearch) {
        testSearch.addEventListener('input', testSearchFunction);
    }
    
    if (testBtn) {
        testBtn.addEventListener('click', testSearchFunction);
    }
    
    testSearchFunction();
});
</script>
@endsection
