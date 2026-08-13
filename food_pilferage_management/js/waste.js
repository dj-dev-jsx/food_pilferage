let currentPage = 1;
const itemsPerPage = 10;
const REFRESH_INTERVAL = 5000;

function fetchWasteRecords(page = 1) {
    $.ajax({
        url: 'processes/waste_table.php',
        method: 'POST',
        dataType: 'json',
        data: {
            action: 'fetchData',
            page: page,
            items_per_page: itemsPerPage,
            search: $('#search_inp').val(),
            date: $('#date_filter').val(),
            user: $('#user_filter').val()
        },
        success: function(response) {
            $('#waste-records').html(response.records);
            updatePagination(response.total_records, page);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching waste records:', error);
        }
    });
}

$(document).ready(function() {
    fetchWasteRecords();
    
    const refreshInterval = setInterval(function() {
        fetchWasteRecords(currentPage);
    }, REFRESH_INTERVAL);

    const filterInputs = $('#search_inp, #date_filter, #user_filter');
    filterInputs.on('change keyup', debounce(function() {
        currentPage = 1;
        fetchWasteRecords(1);
    }, 300));
});

$(document).on('click', '.pagination-controls button', function() {
    currentPage = $(this).data('page');
    fetchWasteRecords(currentPage);
});

function updatePagination(totalRecords, currentPage) {
    const totalPages = Math.ceil(totalRecords / itemsPerPage);
    const start = Math.max(((currentPage - 1) * itemsPerPage) + 1, 1);
    const end = Math.min(start + itemsPerPage - 1, totalRecords);
    
    $('#entries-start').text(totalRecords > 0 ? start : 0);
    $('#entries-end').text(end);
    $('#total-entries').text(totalRecords);
    
    $('#prev-page').prop('disabled', currentPage <= 1);
    $('#next-page').prop('disabled', currentPage >= totalPages);
    
    const pageButtons = Array.from({length: totalPages}, (_, i) => 
        `<button class="btn btn-outline-success ${i + 1 === currentPage ? 'active' : ''}"
         data-page="${i + 1}">${i + 1}</button>`
    ).join('');
    
    $('#page-numbers').html(pageButtons);
}

function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}
