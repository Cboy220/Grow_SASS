<script>
const initAnalayze = function () {
  const url = $('#askAI').data('init-url');

  $.ajax({
    url,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (res) {
      if (res.success) {
        $('#summaryContent').html(`<i class="fas fa-check-circle text-success mr-1"></i> ${marked.parse(res.result)}`);
      } else {
        NX.notification({
          type: 'error',
          message: res.message
        })
      }
    },
    error: function (err) {
      NX.notification({
        type: 'error',
        message: err.message
      });
      console.log(err);
    }
  })
};
$(document).ready(function () {
    initAnalayze();

  // Ask AI a question
  $('#askAI').on('click', function () {
    const url = $(this).data('action-url');
    const question = $('#clientQuestion').val().trim();
    if (!question) return;

    $('#aiAnswer').removeClass('d-none').html(`<i class="fas fa-spinner fa-spin text-muted mr-2"></i> <?php echo e(__('lang.thinking')); ?>`);

    $.ajax({
        url,
        type: 'POST',
        data: {
            question
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            $('#aiAnswer').html(`<i class="fas fa-robot text-info mr-2"></i>${marked.parse(res.answer)}`);
        },
        error: function() {
            $('#aiAnswer').html(`<i class="fas fa-times-circle text-danger mr-2"></i> <?php echo e(__('lang.error_reaching_ai')); ?>`);
        }
    });
  });
});
</script>
<?php /**PATH D:\my_apache\application\resources\views/pages/client/components/tabs/clientai/js.blade.php ENDPATH**/ ?>