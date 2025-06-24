<div id="feedbackList">
    {{-- Title of this tab --}}
    <h5 class="mb-3 font-weight-bold">{{__('lang.customer_feedback')}}</h5>
    <p class="mb-3 text-light small">{{ __('lang.feedback_subtitle') }}</p>
  @forelse ($feedbacks as $index => $fb)
      <div class="feedback-block">
          <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
              <div class="d-flex align-items-center" style="flex-wrap: wrap; flex: 1; min-width: 0;">
                  <div class="score-badge ml-3 mr-3" style="min-width: 50px; text-align: center;">
                      {{ number_format($fb->total_marks, 1) }}
                  </div>
                  <div style="flex: 1; min-width: 0;">
                      <div class="text-muted small">{{ $fb->feedback_date_human ?? \Carbon\Carbon::parse($fb->feedback_date)->diffForHumans() }}</div>
                      <div class="font-weight-bold text-break">"{{ $fb->comment }}"</div>
                  </div>
              </div>

              <div class="action-area ml-2 mt-2 mt-md-0">
                  <div class="feedback-stars text-right">
                      @php
                          $stars = $fb->total_marks / 2;
                          $fullStars = floor($stars) + (($stars % 1 >= 0.75) ? 1 : 0);
                          $hasHalf = ($stars % 1 >= 0.25 && $stars % 1 < 0.75);
                          $emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);
                      @endphp

                      @for ($i = 0; $i < $fullStars; $i++)
                          <i class="fas fa-star text-warning"></i>
                      @endfor
                      @if ($hasHalf)
                          <i class="fas fa-star-half-alt text-warning"></i>
                      @endif
                      @for ($i = 0; $i < $emptyStars; $i++)
                          <i class="far fa-star text-warning"></i>
                      @endfor
                  </div>
              </div>
          </div>
      </div>
  @empty
      <div class="feedback-block alert-danger">
          {{ __('lang.no_feedback_available') }}
      </div>
  @endforelse
</div>
