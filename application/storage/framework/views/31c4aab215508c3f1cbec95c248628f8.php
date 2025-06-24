<div class="modal-dialog" role="document">
  <form id="expectationForm"
        data-id="<?php echo e($id); ?>"
        data-action-url="<?php echo e(route($page['action_route'], $id)); ?>"
        data-method="<?php echo e(isset($expectation) ? 'PUT' : 'POST'); ?>"
        class="modal-content needs-validation"
        novalidate>

      
      <div class="modal-header bg-primary text-white">
          <h5 class="modal-title text-white" id="expectationModalLabel">
              <i class="fas fa-bullseye mr-2"></i>
              <?php echo e(isset($expectation) ? __('lang.edit_client_expectation') : __('lang.new_client_expectation')); ?>

          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>

      
      <div class="modal-body">
          
          <div class="form-group">
              <label for="title"><i class="fas fa-heading mr-1"></i><?php echo e(__('lang.title')); ?></label>
              <input type="text" class="form-control" id="title" name="title"
                  value="<?php echo e($expectation->title ?? ''); ?>"
                  placeholder="<?php echo e(__('lang.short_clear_title')); ?>" required maxlength="255" />
              <div class="invalid-feedback"><?php echo e(__('lang.please_enter_title')); ?></div>
          </div>

          
          <div class="form-group">
              <label for="content"><i class="fas fa-align-left mr-1"></i><?php echo e(__('lang.description')); ?></label>
              <textarea class="form-control" id="content" name="content" rows="3"
                  placeholder="<?php echo e(__('lang.optional_detailed_description')); ?>"
                  maxlength="1000"><?php echo e($expectation->content ?? ''); ?></textarea>
              <small class="form-text text-muted"><?php echo e(__('lang.max_1000_characters')); ?></small>
          </div>

          
          <div class="form-group">
              <label for="weight"><i class="fas fa-weight-hanging mr-1"></i><?php echo e(__('lang.weight_importance')); ?></label>
              <input type="number" class="form-control" id="weight" name="weight" min="0" step="0.1"
                  value="<?php echo e($expectation->weight ?? '1.0'); ?>" required />
              <div class="invalid-feedback"><?php echo e(__('lang.please_enter_valid_weight')); ?></div>
          </div>

          
          <div class="form-group">
              <label for="due_date"><i class="fas fa-calendar-alt mr-1"></i><?php echo e(__('lang.due_date')); ?></label>
              <input type="date" class="form-control" id="due_date" name="due_date"
                  value="<?php echo e(isset($expectation) ? \Carbon\Carbon::parse($expectation->due_date)->format('Y-m-d') : ''); ?>"
                  required />
              <div class="invalid-feedback"><?php echo e(__('lang.please_select_due_date')); ?></div>
          </div>

          
          <div class="form-group">
              <label for="status"><i class="fas fa-toggle-on mr-1"></i><?php echo e(__('lang.status')); ?></label>
              <select class="form-control" id="status" name="status" required>
                  <option value="pending" <?php echo e((isset($expectation) && $expectation->status === 'pending') ? 'selected' : ''); ?>><?php echo e(__('lang.pending')); ?></option>
                  <option value="fulfilled" <?php echo e((isset($expectation) && $expectation->status === 'fulfilled') ? 'selected' : ''); ?>><?php echo e(__('lang.fulfilled')); ?></option>
              </select>
              <div class="invalid-feedback"><?php echo e(__('lang.please_select_status')); ?></div>
          </div>
      </div>

      
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <?php echo e(__('lang.cancel')); ?>

          </button>
          <button type="submit" class="btn btn-primary">
              <i class="fas fa-check mr-1"></i>
              <?php echo e(isset($expectation) ? __('lang.update') : __('lang.save_expectation')); ?>

          </button>
      </div>
  </form>
</div>
<?php /**PATH D:\my_apache\application\resources\views/pages/client/components/modals/add-edit-expectation.blade.php ENDPATH**/ ?>