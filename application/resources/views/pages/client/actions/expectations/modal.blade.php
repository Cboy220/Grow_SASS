<div class="modal fade" id="expectationModal" tabindex="-1" role="dialog" aria-labelledby="expectationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="expectationForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="expectationModalLabel">{{ __('lang.add_edit_expectation') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="expectation-id">
                    <div class="form-group">
                        <label for="goal">{{ __('lang.goal') }}</label>
                        <input type="text" class="form-control" name="goal" id="goal" required>
                    </div>
                    <div class="form-group">
                        <label for="status">{{ __('lang.status') }}</label>
                        <select class="form-control" name="status" id="status">
                            <option value="pending">{{ __('lang.pending') }}</option>
                            <option value="completed">{{ __('lang.completed') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="target_date">{{ __('lang.target_date') }}</label>
                        <input type="date" class="form-control" name="target_date" id="target_date" required>
                    </div>
                    <div class="form-group">
                        <label for="completion_percentage">{{ __('lang.completion_percent') }}</label>
                        <input type="number" class="form-control" name="completion_percentage" id="completion_percentage" min="0" max="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('lang.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div> 