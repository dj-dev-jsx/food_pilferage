let currentPage = 1;
const itemsPerPage = 10;

// Event listeners for filters
$('#searchLog, #dateFilter, #actionFilter').on('change keyup', function() {
    currentPage = 1;
    loadLogs(1);
});

function loadLogs(page = 1) {
    $.ajax({
        url: 'processes/inventory_logs_process.php',
        type: 'POST',
        data: {
            action: 'search',
            page: page,
            items_per_page: itemsPerPage,
            search_term: $('#searchLog').val(),
            date_filter: $('#dateFilter').val(),
            action_filter: $('#actionFilter').val()
        },
        success: function(response) {
            const data = JSON.parse(response);
            $('#logRecords').html(data.records);
            updatePagination(data.total_records, page);
        }
    });
}

function updatePagination(totalRecords, currentPage) {
    const totalPages = Math.ceil(totalRecords / itemsPerPage);
    const start = ((currentPage - 1) * itemsPerPage) + 1;
    const end = Math.min(start + itemsPerPage - 1, totalRecords);
    
    $('#entries-start').text(totalRecords > 0 ? start : 0);
    $('#entries-end').text(end);
    $('#total-entries').text(totalRecords);
    
    $('#prev-page').prop('disabled', currentPage === 1);
    $('#next-page').prop('disabled', currentPage >= totalPages);
    
    let pageButtons = '';
    for(let i = 1; i <= totalPages; i++) {
        pageButtons += `<button class="btn ${i === currentPage ? 'btn-success' : 'btn-outline-success'}" 
                        onclick="loadLogs(${i})">${i}</button>`;
    }
    $('#page-numbers').html(pageButtons);
}

$(document).ready(function() {
    // Initial load
    loadLogs(1);

    // Pagination controls
    $('#prev-page').click(function() {
        if (currentPage > 1) {
            currentPage--;
            loadLogs(currentPage);
        }
    });

    $('#next-page').click(function() {
        currentPage++;
        loadLogs(currentPage);
    });

    // Export functionality
    $('#exportCSV').click(function() {
        window.location.href = 'processes/export_logs_csv.php';
    });

    // Page number clicks
    $(document).on('click', '#page-numbers button', function() {
        currentPage = parseInt($(this).text());
        loadLogs(currentPage);
    });
});
