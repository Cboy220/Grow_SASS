<script>
    function loadQueries() {
        $.get(`/settings/feedback/gettbody`, function(res) {
            $('#queryTableBody').html(res.tbodyHtml);
        });
    }

    $('#querySearchBtn').click(() => loadQueries());

    $(document).on('click', '#addQueryBtn', function() {
        const id = `new-${Date.now()}`;
        const actionUrl = $(this).data('action-url');
        console.log(actionUrl);
        const row = `
    <tr data-id="${id}" class="editable-row">
      <td>New</td>
      <td><input type="text" class="form-control" style="min-width: 250px;" name="title"></td>
      <td><textarea class="form-control" name="content" rows="2" style="min-width: 300px;"></textarea></td>
      <td>
        <select class="form-control form-control-sm" name="type">
          <option value="1">Numeric</option>
          <option value="2">Star</option>
          <option value="3">Select</option>
        </select>
      </td>
      <td>
        <div class="d-flex align-items-center">
          <input type="range" class="custom-range mr-2" name="range" min="1" max="20" step="1" value="5">
          <input type="number" class="form-control form-control-sm" name="rangeDisplay" min="1" max="20" value="5" style="width: 60px;">
        </div>
      </td>
      <td>
        <div class="d-flex align-items-center">
          <input type="range" class="custom-range mr-2" name="weight" min="1" max="10" step="0.1" value="1.0">
          <input type="number" class="form-control form-control-sm" name="weightDisplay" min="1" max="10" step="0.1" value="1.0" style="width: 70px;">
        </div>
      </td>
      <td>
        <button class="btn btn-sm btn-success save-new-query" data-action-url="${actionUrl}"><i class="fas fa-check"></i></button>
        <button class="btn btn-sm btn-secondary cancel-new-query"><i class="fas fa-times"></i></button>
      </td>
    </tr>
  `;
        $('#queryTableBody').prepend(row);
    });

    $(document).on('click', '.cancel-new-query', function() {
        $(this).closest('tr').remove();
    });

    $(document).on('click', '.save-new-query', function() {
        const $row = $(this).closest('tr');
        const data = {
            title: $row.find('[name="title"]').val(),
            content: $row.find('[name="content"]').val(),
            type: $row.find('[name="type"]').val(),
            range: $row.find('[name="range"]').val(),
            weight: $row.find('[name="weight"]').val(),
        };
        const url = $(this).data('action-url');

        $.ajax({
            url,
            type: 'POST',
            data,
            headers: {
                "X-CSRF-TOKEN": NX.csrf_token
            },
            success: (res) => {
                loadQueries();
                NX.notification({
                    type: res.notification.type,
                    message: res.notification.value
                });
            },
            error: (err) => {
                NX.notification({
                    type: 'error',
                    message: err.message
                });
                console.log(err);
            }
        });
    });

    $(document).on('click', '.edit-query', function() {
        const $row = $(this).closest('tr');
        const id = $(this).data('id');
        const title = $row.find('.static-title').text().trim();
        const content = $row.find('.static-content').text().trim();
        const type = $row.find('.static-type').data('value');
        const range = $row.find('.static-range').text().trim();
        const weight = $row.find('.static-weight').text().trim();
        const actionUrl = $(this).data('action-url');
        $row.html(`
    <td>${id}</td>
    <td><input type="text" class="form-control" style="min-width: 250px;" name="title" value="${title}"></td>
    <td><textarea class="form-control" name="content" rows="2" style="min-width: 300px;">${content}</textarea></td>
    <td>
      <select class="form-control form-control-sm" name="type">
        <option value="1" ${type == 1 ? 'selected' : ''}>Numeric</option>
        <option value="2" ${type == 2 ? 'selected' : ''}>Star</option>
        <option value="3" ${type == 3 ? 'selected' : ''}>Select</option>
      </select>
    </td>
    <td>
      <div class="d-flex align-items-center">
        <input type="range" class="custom-range mr-2" name="range" min="1" max="20" step="1" value="${range}">
        <input type="number" class="form-control form-control-sm" name="rangeDisplay" min="1" max="20" value="${range}" style="width: 60px;">
      </div>
    </td>
    <td>
      <div class="d-flex align-items-center">
        <input type="range" class="custom-range mr-2" name="weight" min="1" max="10" step="0.1" value="${weight}">
        <input type="number" class="form-control form-control-sm" name="weightDisplay" min="1" max="10" step="0.1" value="${weight}" style="width: 70px;">
      </div>
    </td>
    <td>
      <button class="btn btn-sm btn-primary save-edit-query" data-action-url="${actionUrl}" data-id="${id}"><i class="fas fa-save"></i></button>
      <button class="btn btn-sm btn-secondary cancel-edit-query"><i class="fas fa-times"></i></button>
    </td>
  `);
    });

    $(document).on('click', '.save-edit-query', function() {
        const $row = $(this).closest('tr');
        const id = $(this).data('id');
        const data = {
            title: $row.find('[name="title"]').val(),
            content: $row.find('[name="content"]').val(),
            type: $row.find('[name="type"]').val(),
            range: $row.find('[name="range"]').val(),
            weight: $row.find('[name="weight"]').val(),
        };
        const url = $(this).data('action-url');
        $.ajax({
            url,
            method: 'PUT',
            data,
            headers: {
                "X-CSRF-TOKEN": NX.csrf_token
            },
            success: (res) => {
                loadQueries();
                NX.notification({
                    type: res.notification.type,
                    message: res.notification.value
                });
            },
            error: (err) => NX.notification({
                type: 'error',
                message: err.message
            })
        });
    });

    $(document).on('click', '.cancel-edit-query', function() {
        loadQueries();
    });

    ((document, $) => {
        let pendingDeleteUrl = null;

        $(document).on('click', '.delete-query', function() {
            pendingDeleteUrl = $(this).data('action-url');
            $('#confirmOverlay').fadeIn(200);
        });

        $('#confirmDeleteYes').click(function() {
            if (!pendingDeleteUrl) return;
            $.ajax({
                url: pendingDeleteUrl,
                method: 'DELETE',
                headers: {
                    "X-CSRF-TOKEN": NX.csrf_token
                },
                success: (res) => {
                    $('#confirmOverlay').fadeOut(100);
                    loadQueries();
                    NX.notification({
                        type: res.notification.type,
                        message: res.notification.value
                    });
                },
                error: (err) => {
                    $('#confirmOverlay').fadeOut(100);
                    NX.notification({
                        type: 'error',
                        message: err.message
                    });
                }
            });
        });

        $('#confirmDeleteNo').click(function() {
            $('#confirmOverlay').fadeOut(100);
            pendingDeleteId = null;
        });
    })(document, $)

    $(document).on('input', '[name="range"]', function() {
        $(this).closest('td').find('[name="rangeDisplay"]').val(this.value);
    });

    $(document).on('input', '[name="rangeDisplay"]', function() {
        const val = $(this).val();
        $(this).closest('td').find('[name="range"]').val(val);
    });

    $(document).on('input', '[name="weight"]', function() {
        $(this).closest('td').find('[name="weightDisplay"]').val(this.value);
    });

    $(document).on('input', '[name="weightDisplay"]', function() {
        const val = $(this).val();
        $(this).closest('td').find('[name="weight"]').val(val);
    });

    $(document).ready(() => loadQueries());
</script>
<?php /**PATH D:\my_apache\application\resources\views/pages/settings/sections/feedback/js.blade.php ENDPATH**/ ?>