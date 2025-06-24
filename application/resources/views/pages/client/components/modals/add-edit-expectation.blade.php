<div class="modal-dialog" role="document">
  <form id="expectationForm"
        data-id="{{ $id }}"
        data-action-url="{{ route($page['action_route'], $id) }}"
        data-method="{{ isset($expectation) ? 'PUT' : 'POST'}}"
        class="modal-content needs-validation"
        novalidate>

      {{-- Header --}}
      <div class="modal-header bg-primary text-white">
          <h5 class="modal-title text-white" id="expectationModalLabel">
              <i class="fas fa-bullseye mr-2"></i>
              {{ isset($expectation) ? __('lang.edit_client_expectation') : __('lang.new_client_expectation') }}
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>

      {{-- Body --}}
      <div class="modal-body">
          {{-- Title --}}
          <div class="form-group">
              <label for="title"><i class="fas fa-heading mr-1"></i>{{ __('lang.title') }}</label>
              <input type="text" class="form-control" id="title" name="title"
                  value="{{ $expectation->title ?? '' }}"
                  placeholder="{{ __('lang.short_clear_title') }}" required maxlength="255" />
              <div class="invalid-feedback">{{ __('lang.please_enter_title') }}</div>
          </div>

          {{-- Content --}}
          <div class="form-group">
              <label for="content"><i class="fas fa-align-left mr-1"></i>{{ __('lang.description') }}</label>
              <textarea class="form-control" id="content" name="content" rows="3"
                  placeholder="{{ __('lang.optional_detailed_description') }}"
                  maxlength="1000">{{ $expectation->content ?? '' }}</textarea>
              <small class="form-text text-muted">{{ __('lang.max_1000_characters') }}</small>
          </div>

          {{-- Weight --}}
          <div class="form-group">
              <label for="weight"><i class="fas fa-weight-hanging mr-1"></i>{{ __('lang.weight_importance') }}</label>
              <input type="number" class="form-control" id="weight" name="weight" min="0" step="0.1"
                  value="{{ $expectation->weight ?? '1.0' }}" required />
              <div class="invalid-feedback">{{ __('lang.please_enter_valid_weight') }}</div>
          </div>

          {{-- Due Date --}}
          <div class="form-group">
              <label for="due_date"><i class="fas fa-calendar-alt mr-1"></i>{{ __('lang.due_date') }}</label>
              <input type="date" class="form-control" id="due_date" name="due_date"
                  value="{{ isset($expectation) ? \Carbon\Carbon::parse($expectation->due_date)->format('Y-m-d') : '' }}"
                  required />
              <div class="invalid-feedback">{{ __('lang.please_select_due_date') }}</div>
          </div>

          {{-- Status --}}
          <div class="form-group">
              <label for="status"><i class="fas fa-toggle-on mr-1"></i>{{ __('lang.status') }}</label>
              <select class="form-control" id="status" name="status" required>
                  <option value="pending" {{ (isset($expectation) && $expectation->status === 'pending') ? 'selected' : '' }}>{{ __('lang.pending') }}</option>
                  <option value="fulfilled" {{ (isset($expectation) && $expectation->status === 'fulfilled') ? 'selected' : '' }}>{{ __('lang.fulfilled') }}</option>
              </select>
              <div class="invalid-feedback">{{ __('lang.please_select_status') }}</div>
          </div>
      </div>

      {{-- Footer --}}
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
              {{ __('lang.cancel') }}
          </button>
          <button type="submit" class="btn btn-primary">
              <i class="fas fa-check mr-1"></i>
              {{ isset($expectation) ? __('lang.update') : __('lang.save_expectation') }}
          </button>
      </div>
  </form>
</div>
