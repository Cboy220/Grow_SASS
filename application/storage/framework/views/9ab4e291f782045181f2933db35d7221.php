<script>
    let currentPage = 1;
    let search = '';

    function loadExpectations() {
        const url = 'expectation/get_expectations';
        $.ajax({
            url,
            method: 'GET',
            data: {
                page: currentPage,
                search: search
            },
            success: function(res) {
                $('#expectationStatsContainer').html(res.statsHtml);
                $('#expectationListContainer').html(res.listHtml);
            },
            error: function(err) {
                console.log('ajax page load error: ', err);
                setTimeout(() => {
                    loadExpectations();
                }, 10);
            }
        });
    }

    $('#searchBtn').click(function() {
        search = $('#searchInput').val();
        currentPage = 1;
        loadExpectations();
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.key === 'Enter') {
            search = $(this).val();
            currentPage = 1;
            loadExpectations();
        }
    });

    // Enable AJAX pagination
    $(document).on('click', '#expectationListContainer .pagination a', function(e) {
        e.preventDefault();
        const page = $(this).attr('href').split('page=')[1];
        currentPage = parseInt(page);
        loadExpectations();
    });

    $(document).ready(function() {
        loadExpectations();
    });
</script>
<?php /**PATH D:\my_apache\application\resources\views/pages/expectation/js.blade.php ENDPATH**/ ?>