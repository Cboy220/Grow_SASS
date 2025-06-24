<script>

  $(document).ready(function () {
    $('#client-analysis-form').on('submit', function (e) {
        e.preventDefault();
        
        let formData = $(this).serialize();
        const actionUrl = $(this).data('action-url');
        $('#ai-result-box').addClass('d-none');
        $('#ai-response-text').html('<i class="fas fa-spinner fa-spin text-info"></i> Generating...');
        console.log(formData);
        $.ajax({
            url: actionUrl,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            success: function (res) {
                $('#ai-response-text').html(res.response.result);
                $('#ai-result-box').removeClass('d-none');
            },
            error: function () {
                $('#ai-response-text').html('<span class="text-danger">Failed to generate analysis.</span>');
                $('#ai-result-box').removeClass('d-none');
            }
        });
    });
  });
  </script><?php /**PATH D:\my_apache\application\resources\views/pages/clientai/js.blade.php ENDPATH**/ ?>