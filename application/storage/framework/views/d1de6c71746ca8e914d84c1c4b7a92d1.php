<div class="doc-to-by-container">


    <!--scheduled for publishing-->
    <?php if($document->doc_status == 'draft' && $document->doc_publishing_type == 'scheduled'): ?>
    <?php if($document->doc_publishing_scheduled_status == 'pending'): ?>
    <div class="alert alert-info m-b-0 m-t-5 m-b-20"><?php echo app('translator')->get('lang.scheduled_publishing_info'); ?> :
        <?php echo e(runtimeDate($document->doc_publishing_scheduled_date)); ?></div>
    <?php endif; ?>
    <?php if($document->doc_publishing_scheduled_status == 'failed'): ?>
    <div class="alert alert-danger m-b-0 m-t-5 m-b-20"><?php echo app('translator')->get('lang.scheduled_publishing_failed_info'); ?> :
        <?php echo e(runtimeDate($document->doc_publishing_scheduled_date)); ?></div>
    <?php endif; ?>
    <?php endif; ?>

    <div class="row">

        <!--COMPANY DETAILS-->
        <div class="col-6">
            <div class="doc-to-by">
                <!--title-->
                <div class="">
                    <h3><?php echo app('translator')->get('lang.service_provider'); ?></h3>
                </div>
                <!--organisation & address-->
                <div class="x-title resizetext">
                    <h4 class="font-weight-500"><?php echo e(config('system.settings_company_name')); ?></h4>
                    <?php if(config('system.settings_company_address_line_1')): ?>
                    <div><?php echo e(config('system.settings_company_address_line_1')); ?></div>
                    <?php endif; ?>
                    <?php if(config('system.settings_company_city')): ?>
                    <div><?php echo e(config('system.settings_company_city')); ?></div>
                    <?php endif; ?>
                    <?php if(config('system.settings_company_state')): ?>
                    <div><?php echo e(config('system.settings_company_state')); ?></div>
                    <?php endif; ?>
                    <?php if(config('system.settings_company_zipcode')): ?>
                    <div><?php echo e(config('system.settings_company_zipcode')); ?></div>
                    <?php endif; ?>
                    <?php if(config('system.settings_company_country')): ?>
                    <div><?php echo e(config('system.settings_company_country')); ?></div>
                    <?php endif; ?>
                    <?php if(config('system.settings_company_customfield_1') != ''): ?>
                    <div><?php echo e(config('system.settings_company_customfield_1')); ?></div>
                    <?php endif; ?>
                    <?php if(config('system.settings_company_customfield_2') != ''): ?>
                    <div><?php echo e(config('system.settings_company_customfield_2')); ?></div>
                    <?php endif; ?>
                    <?php if(config('system.settings_company_customfield_3') != ''): ?>
                    <div><?php echo e(config('system.settings_company_customfield_3')); ?></div>
                    <?php endif; ?>
                    <?php if(config('system.settings_company_customfield_4') != ''): ?>
                    <div><?php echo e(config('system.settings_company_customfield_4')); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>


        <!--CLIENT RESOURCE-->
        <?php if($document->docresource_type == 'client'): ?>
        <div class="col-6">
            <div class="doc-to-by text-right">
                <!--title-->
                <div class="">
                    <h3><?php echo app('translator')->get('lang.client'); ?></h3>
                </div>
                <!--organisation & address-->
                <div class="x-title resizetext">
                    <h4 class="font-weight-500"><?php echo e($document->client_company_name); ?></h4>
                    <?php if($document->client_billing_street): ?>
                    <div><?php echo e($document->client_billing_street); ?></div>
                    <?php endif; ?>
                    <?php if($document->client_billing_city): ?>
                    <div><?php echo e($document->client_billing_city); ?></div>
                    <?php endif; ?>
                    <?php if($document->client_billing_state): ?>
                    <div><?php echo e($document->client_billing_state); ?></div>
                    <?php endif; ?>
                    <?php if($document->client_billing_zip): ?>
                    <div><?php echo e($document->client_billing_zip); ?></div>
                    <?php endif; ?>
                    <?php if($document->client_billing_country): ?>
                    <div><?php echo e($document->client_billing_country); ?></div>
                    <?php endif; ?>
                    <!--custom fields-->
                    <?php $__currentLoopData = $customfields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($field->customfields_show_invoice == 'yes' && $field->customfields_status == 'enabled'): ?>
                    <?php $key = $field->customfields_name; ?>
                    <?php $customfield = $document[$key] ?? ''; ?>
                    <?php if($customfield != ''): ?>
                    <div class="text-muted m-t-3"><?php echo e($field->customfields_title); ?>:
                        <?php echo e(runtimeCustomFieldsFormat($customfield, $field->customfields_datatype)); ?></div>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>



        <!--LEAD RESOURCE-->
        <?php if($document->docresource_type == 'lead'): ?>
        <div class="col-sm-12 col-lg-6">
            <div class="doc-to-by text-right">
                <!--title-->
                <div class="">
                    <h3><?php echo app('translator')->get('lang.client'); ?></h3>
                </div>
                <!--organisation & address-->
                <div class="x-title resizetext">
                    <?php if($document->lead_company_name): ?>
                    <h4 class="font-weight-500"><?php echo e($document->lead_company_name); ?></h4>
                    <?php else: ?>
                    <h4 class="font-weight-500"><?php echo e($document->lead_firstname); ?> <?php echo e($document->lead_lastname); ?>

                    </h4>
                    <?php endif; ?>
                    <?php if($document->lead_street): ?>
                    <div><?php echo e($document->lead_street); ?></div>
                    <?php endif; ?>
                    <?php if($document->lead_city): ?>
                    <div><?php echo e($document->lead_city); ?></div>
                    <?php endif; ?>
                    <?php if($document->lead_state): ?>
                    <div><?php echo e($document->lead_state); ?></div>
                    <?php endif; ?>
                    <?php if($document->lead_zip): ?>
                    <div><?php echo e($document->lead_zip); ?></div>
                    <?php endif; ?>
                    <?php if($document->lead_country): ?>
                    <div><?php echo e($document->lead_country); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>





    </div>
</div><?php /**PATH D:\Grow_SaaS\application\resources\views/pages/documents/elements/doc-to-by.blade.php ENDPATH**/ ?>