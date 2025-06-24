  <div class="card shadow-sm rounded-4 p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
          <h4><i class="fas fa-question-circle text-primary mr-2"></i>Feedback Questions</h4>
          <button class="btn btn-outline-primary" id="addQueryBtn" data-action-url="<?php echo e(route('settings.feedback.create')); ?>">
              <i class="fas fa-plus"></i> Add Question
          </button>
      </div>

      <!-- List table -->
      <div class="table-responsive">
          <table class="table table-hover table-bordered " id="queryTable">
              <thead class="thead-light">
                  <tr>
                      <th>#</th>
                      <th>Title</th>
                      <th>Content</th>
                      <th>Type</th>
                      <th>Range</th>
                      <th>Weight</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody id="queryTableBody">
                  <!-- Rendered from Blade via AJAX -->
              </tbody>
          </table>
      </div>
  </div>
  <!-- Custom confirm box -->
  <div id="confirmOverlay"
      style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
      <div class="d-flex justify-content-center align-items-center" style="height:100%;">
          <div class="bg-white rounded p-4 text-center" style="width:300px;">
              <p class="mb-3">Are you sure you want to delete this query?</p>
              <div class="d-flex justify-content-around">
                  <button class="btn btn-danger btn-sm" id="confirmDeleteYes">Yes</button>
                  <button class="btn btn-secondary btn-sm" id="confirmDeleteNo">No</button>
              </div>
          </div>
      </div>
  </div>
  <?php echo $__env->make('pages.settings.sections.feedback.js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH D:\my_apache\application\resources\views/pages/settings/sections/feedback/page.blade.php ENDPATH**/ ?>