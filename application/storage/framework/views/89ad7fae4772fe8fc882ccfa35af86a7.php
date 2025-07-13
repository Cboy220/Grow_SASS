<div class="doc-dates-container" id="doc-details">
    <div class="doc-dates-wrapper  <?php echo e(documentEditingModeCheck1($payload['mode'] ?? '')); ?>">
        <!--editing icons-->
        <div class="doc-edit-icon  <?php echo e(documentEditingModeCheck2($payload['mode'] ?? '')); ?>">
            <span class="x-edit-icon js-toggle-side-panel" data-target="documents-side-panel-details">
                <i class="sl-icon-note"></i>
            </span>
        </div>


        <div class="pull-left x-dates">
            <table>
                <tbody>
                    <!--issue date-->
                    <tr id="document_dates_section_1">
                        <td class="x-left-lang font-weight-500">
                            <?php if($document->doc_type == 'proposal'): ?>
                            <span><?php echo app('translator')->get('lang.proposal_date'); ?>:</span>
                            <?php else: ?>
                            <span><?php echo app('translator')->get('lang.contract_start_date'); ?>:</span>
                            <?php endif; ?>
                        </td>
                        <td class="x-left-date p-l-25">
                            <span><?php echo e(runtimeDate($document->doc_date_start)); ?></span>
                        </td>
                    </tr>
                    <!--valid until-->
                    <tr id="document_dates_section_2">
                        <td class="x-left-lang  p-t-10 font-weight-500">
                            <?php if($document->doc_type == 'proposal'): ?>
                            <span><?php echo app('translator')->get('lang.valid_until'); ?>:</span>
                            <?php else: ?>
                            <span><?php echo app('translator')->get('lang.contract_end_date'); ?>:</span>
                            <?php endif; ?>
                        </td>
                        <td class="x-left-id p-l-25 p-t-10">
                            <?php if($document->doc_type == 'proposal'): ?>
                            <span><?php echo e(runtimeDate($document->doc_date_end)); ?></span>
                            <?php else: ?>
                            <span><?php echo e(runtimeDate($document->doc_date_end, __('lang.open_ended'))); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!--prepared by-->
                    <tr id="document_dates_section_3">
                        <td class="x-left-lang p-t-10 font-weight-500">
                            <span id="doc_prepared_by_title"><?php echo app('translator')->get('lang.prepared_by'); ?>:</span> </td>
                        <td class="x-left-id p-l-25 p-t-10">
                            <span id="doc_prepared_by_name"><?php echo e($document->first_name ?? '---'); ?>

                                <?php echo e($document->last_name ?? '---'); ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="pull-right">
            <table>
                <tbody>
                    <!----DOC ID-->
                    <tr id="document_dates_section_4">
                        <td class="x-left-lang p-t-10 font-weight-500">
                            <?php if($document->doc_type == 'proposal'): ?>
                            <span><?php echo app('translator')->get('lang.proposal_id'); ?>:</span>
                            <?php else: ?>
                            <span><?php echo app('translator')->get('lang.contract_id'); ?>:</span>
                            <?php endif; ?> </td>
                        <td class="x-left-id p-l-25 p-t-10">
                            <?php if($document->doc_type == 'proposal'): ?>
                            <span>#<?php echo e(runtimeProposalIdFormat($document->doc_id)); ?></span>
                            <?php else: ?>
                            <span><?php echo e(runtimeContractIdFormat($document->doc_id)); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!--value-->
                    <?php if($document->doc_type == 'contract'): ?>
                    <tr id="document_dates_section_5">
                        <td class="x-left-lang p-t-10 font-weight-500">
                            <span><?php echo app('translator')->get('lang.value'); ?>:</span>
                        <td class="x-left-id p-l-25 p-t-10">
                            <span><?php echo e(runtimeMoneyFormat($document->doc_value ?? 0)); ?></span>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <!--status-->
                    <tr id="document_dates_section_6">
                        <td class="x-right-lang  p-t-10 font-weight-500"><?php echo app('translator')->get('lang.status'); ?>: </td>
                        <td class="x-right-status  p-t-4 p-l-25">
                            <span class="x-right-label">
                                <?php if($document->doc_type == 'proposal'): ?>
                                <label
                                    class="label label-rounded m-b-0 m-t-6 p-l-15 p-r-15 <?php echo e(runtimeProposalStatusColors($document->doc_status, 'label')); ?>"
                                    id="document-status-label"><?php echo e(runtimeLang($document->doc_status)); ?></label>
                                <?php else: ?>
                                <label
                                    class="label label-rounded m-b-0 m-t-6 p-l-15 p-r-15 <?php echo e(runtimeContractStatusColors($document->doc_status, 'label')); ?>"
                                    id="document-status-label"><?php echo e(runtimeLang($document->doc_status)); ?></label>
                                <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="clear-both">
            <!--fix-->
        </div>
    </div>
</div><?php /**PATH D:\Grow_SaaS\application\resources\views/pages/documents/elements/doc-details.blade.php ENDPATH**/ ?>