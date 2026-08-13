let currentPage = 1;
const itemsPerPage = 10;

// Common AJAX configuration
const ajaxConfig = {
    dataType: 'json',
    error: function(xhr, status, error) {
        console.error('AJAX Error:', {xhr, status, error});
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred. Please try again.',
            icon: 'error',
            confirmButtonColor: '#28a745'
        });
    }
};

// Fetch and update functions
function fetchData(page = 1) {
    const searchValue = $('#search_inp').val();
    const dateFilter = $('#date_filter').val();
    const userFilter = $('#user_filter').val();

    $.ajax({
        ...ajaxConfig,
        url: 'processes/used_items_table.php',
        method: 'POST',
        data: {
            action: 'fetchData',
            page: page,
            items_per_page: itemsPerPage,
            search: searchValue,
            date: dateFilter,
            user_id: userFilter
        },
        success: function(response) {
            $('#use-item-records').html(response.records);
            updatePagination(response.total_records, page);
        }
    });
}

function updatePagination(totalRecords, currentPage) {
    const totalPages = Math.ceil(totalRecords / itemsPerPage);
    const start = ((currentPage - 1) * itemsPerPage) + 1;
    const end = Math.min(start + itemsPerPage - 1, totalRecords);
    
    $('#entries-start').text(start);
    $('#entries-end').text(end);
    $('#total-entries').text(totalRecords);
    $('#prev-page').prop('disabled', currentPage === 1);
    $('#next-page').prop('disabled', currentPage === totalPages);
    
    let pageButtons = '';
    for(let i = 1; i <= totalPages; i++) {
        pageButtons += `<button class="btn btn-outline-success ${i === currentPage ? 'active' : ''}" 
                        data-page="${i}">${i}</button>`;
    }
    $('#page-numbers').html(pageButtons);
}

// Initialize and event handlers
$(document).ready(function() {
    fetchData(1);
    
    // Search functionality
    $('#search_inp').keyup(function() {
        currentPage = 1;
        fetchData(currentPage);
    });
    
    // Date and User filters
    $('#date_filter, #user_filter').on('change', function() {
        currentPage = 1;
        fetchData(currentPage);
    });

    // Pagination controls
    $(document).on('click', '#page-numbers button', function() {
        currentPage = parseInt($(this).data('page'));
        fetchData(currentPage);
    });

    $('#prev-page').click(function() {
        if (currentPage > 1) {
            currentPage--;
            fetchData(currentPage);
        }
    });

    $('#next-page').click(function() {
        currentPage++;
        fetchData(currentPage);
    });
});
