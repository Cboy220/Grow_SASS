<script>
$(function() {
    // Open modal for add
    $(document).on('click', '[data-target="#expectationModal"]', function() {
        $('#expectationForm')[0].reset();
        $('#expectation-id').val('');
        $('#expectationModalLabel').text('Add Expectation');
    });

    // Open modal for edit
    $(document).on('click', '.edit-expectation', function() {
        var row = $(this).closest('tr');
        $('#expectation-id').val(row.data('id'));
        $('#goal').val(row.find('td').eq(0).text().trim());
        $('#status').val(row.find('.toggle-status').is(':checked') ? 'completed' : 'pending');
        $('#target_date').val(row.find('td').eq(2).text().trim());
        $('#completion_percentage').val(parseInt(row.find('td').eq(3).text()));
        $('#expectationModalLabel').text('Edit Expectation');
        $('#expectationModal').modal('show');
    });

    // Save (add/edit)
    $('#expectationForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#expectation-id').val();
        var url = id ? '/client/expectations/' + id : '/client/expectations';
        var method = id ? 'PUT' : 'POST';
        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(res) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseText);
            }
        });
    });

    // Toggle status
    $(document).on('change', '.toggle-status', function() {
        var id = $(this).data('id');
        var status = $(this).is(':checked') ? 'completed' : 'pending';
        $.ajax({
            url: '/client/expectations/' + id,
            method: 'PUT',
            data: { status: status },
            success: function(res) {
                location.reload();
            }
        });
    });

    // Delete
    $(document).on('click', '.delete-expectation', function() {
        if (!confirm('Delete this expectation?')) return;
        var id = $(this).data('id');
        $.ajax({
            url: '/client/expectations/' + id,
            method: 'DELETE',
            success: function(res) {
                location.reload();
            }
        });
    });
});
</script> 