<div class="modal-dialog modal-md" id="basicModalContainer">
      
    <form action="" method="post" id="feedbackForm" class="form-horizontal">
        <div class="modal-content">
            <div class="modal-header" id="basicModalHeader">
                <h2 class="mb-4 text-center"><i class="fa-regular fa-star-half-stroke mr-1"></i>{{ __('lang.customer_feedback') }}</h2>
                <button type="button" class="close" data-dismiss="modal"
                    id="basicModalCloseIcon">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body min-h-200" id="basicModalBody">
                <!-- Comentario -->
                <div id="questions-scrollable">
                    <div id="questions-container">
                          @foreach($feedbackQueries as $index => $item) 
                            <div class="form-group">
                                <div class="feedback-block feedback-query-answer">
                                    <div class="pb-3">
                                        <label><strong>{{$item->content}}</strong></label>
                                    </div>
                                    @if($item->type == 1)
                                        <div class="d-flex flex-wrap">
                                            @for($i = 1; $i <= $item->range; $i++)
                                                <button type="button" class="btn btn-outline-info m-1 feedback-mark-button" name="{{$item->feedback_query_id}}" data-question="{{$item->feedback_query_id}}" data-value="{{$i}}">{{$i}}</button>
                                            @endfor
                                        </div>
                                    @endif
                                    @if($item->type == 2)
                                        <div class="d-flex align-items-center editable feedback-stars p-2" data-question="{{$item->feedback_query_id}}">
                                            @for($i = 1; $i <= $item->range; $i++)
                                                    <i class="far editable feedback-star fa-star fa-lg mr-2" role="button" data-value="{{$i}}"></i>
                                            @endfor
                                        </div>
                                    @endif
                                    @if($item->type == 3)
                                        <select class="form-control mt-2" name="{{$item->feedback_query_id}}" data-question="{{$item->feedback_query_id}}">
                                            @for($i = 1; $i <= $item->range; $i++)
                                                <option value="{{$i}}">{{$i}}</button>
                                            @endfor
                                        </select>
                                    @endif
                                </div>
                            </div>
                          @endforeach
                    </div>
                  </div>
    
                <!-- Comentario -->
                <div class="form-group">
                    <label for="comment"><strong>{{ __('lang.comment') }}</strong> <small class="text-muted">({{ __('lang.optional') }})</small></label>
                    {{-- {{var_dump($feedbackQueries)}} --}}
                    <textarea class="form-control" id="comment" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer" id="basicModalFooter">
                    <button type="submit" class="btn btn-primary btn-block">{{ __('lang.send') }}</button>
                    {{-- <button type="button" id="basicModalCloseButton" class="btn btn-rounded-x btn-secondary waves-effect text-left" data-dismiss="modal">{{ cleanLang(__('lang.close')) }}</button>
                    <button type="submit" id="basicModalSubmitButton"
                        class="btn btn-rounded-x btn-danger waves-effect text-left basicModalSubmitButton" data-url="" data-loading-target=""
                        data-ajax-type="POST" data-on-start-submit-button="disable">{{ cleanLang(__('lang.submit')) }}</button> --}}
            </div>
        </div>
    </form>
</div>
