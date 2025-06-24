<!--modal-->
<div class="modal actions-modal" role="dialog" aria-labelledby="foo" id="termsModal" {!! _clean(runtimeAllowCloseModalOptions()) !!}>
    <div class="modal-dialog">
        <form action="" method="post" id="termsModalForm" class="form-horizontal">
            <div class="modal-content">
                <div class="modal-body" id="termsModalBody">
                    
                    {!! config('system.settings_terms_of_service') ?? '' !!}

                </div>
                <div class="modal-footer" id="termsModalFooter">
                    <button type="button" class="btn btn-rounded-x btn-secondary waves-effect text-left" data-dismiss="modal">{{ cleanLang(__('lang.close')) }}</button>
                </div>
            </div>
        </form>
    </div>
</div>