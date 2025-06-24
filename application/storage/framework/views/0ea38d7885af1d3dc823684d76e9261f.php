<!--details-->
<div class="col-sm-12 col-lg-3" id="ticket-left-panel">
    <div class="card">
        <div class="row">
            <div class="col-lg-12">
                <div class="ticket-panel">
                    <div class="x-top-header">
                        <?php echo e(cleanLang(__('lang.ticket_details'))); ?>

                    </div>

                    <div class="x-body">

                        <?php if(config('visibility.show_archived_warning')): ?>
                        <div class="alert alert-warning"><?php echo app('translator')->get('lang.ticket_is_archived'); ?></div>
                        <?php endif; ?>

                        <!--department-->
                        <div class="x-list">
                            <div class="x-name"><?php echo e(cleanLang(__('lang.department'))); ?></div>
                            <div class="x-details"><?php echo e($ticket->category_name); ?></div>
                        </div>

                        <!--date-->
                        <div class="x-list">
                            <div class="x-name"><?php echo e(cleanLang(__('lang.created'))); ?></div>
                            <div class="x-details"><?php echo e(runtimeDate($ticket->ticket_created)); ?>

                                <?php echo e(cleanLang(__('lang.at'))); ?>

                                <?php echo e(runtimeTime($ticket->ticket_created)); ?></div>
                        </div>

                        <!--client-->
                        <?php if(auth()->user()->is_team): ?>
                        <div class="x-list">
                            <div class="x-name"><?php echo e(cleanLang(__('lang.client'))); ?></div>
                            <div class="x-details">
                                <a href="/clients/<?php echo e($ticket->ticket_clientid); ?>"
                                    title="<?php echo e($ticket->client_company_name); ?>"><?php echo e(str_limit($ticket->client_company_name ?? '---', 35)); ?></a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!--project-->
                        <div class="x-list">
                            <div class="x-name"><?php echo e(cleanLang(__('lang.project'))); ?></div>
                            <div class="x-details">
                                <a href="/projects/<?php echo e($ticket->ticket_projectid); ?>"
                                    title="<?php echo e($ticket->project_title); ?>"><?php echo e(str_limit($ticket->project_title ?? '---', 30)); ?></a></div>
                        </div>


                        <!--last activity-->
                        <div class="x-list">
                            <div class="x-name"><?php echo e(cleanLang(__('lang.activity'))); ?></div>
                            <div class="x-details"><?php echo e(runtimeDateAgo($ticket->ticket_last_updated)); ?></div>
                        </div>


                        <!--tags-->
                        <div class="x-list">
                            <div class="x-name"><?php echo e(cleanLang(__('lang.tags'))); ?></div>
                            <div class="x-details p-t-8">
                                <?php $__currentLoopData = $ticket->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="label label-outline-info display-inline-block m-b-8 m-r-8"><?php echo e(str_limit($tag->tag_title, 50)); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($field->customfields_show_ticket_page == 'yes'): ?>
                        <div class="x-list">
                            <div class="x-name"><?php echo e($field->customfields_title); ?></div>
                            <div class="x-details"><?php echo clean(customFieldValue($field->customfields_name, $ticket,
                                $field->customfields_datatype)); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <!--priority-->
                        <div class="x-list">
                            <div class="x-name"><?php echo e(cleanLang(__('lang.priority'))); ?></div>
                            <div class="x-details"><span
                                    class="label <?php echo e(runtimeTicketPriorityColors($ticket->ticket_priority, 'label')); ?>"><?php echo e(runtimeLang($ticket->ticket_priority)); ?></span></div>
                        </div>

                        <!--status-->
                        <div class="x-list">
                            <div class="x-name"><?php echo e(cleanLang(__('lang.status'))); ?></div>
                            <div class="x-details"><span class="label label-outline-<?php echo e($ticket->ticketstatus_color); ?>"><?php echo e(runtimeLang($ticket->ticketstatus_title)); ?></span></div>
                        </div>

                        <!--edit button-->
                        <?php if(config('visibility.action_buttons_edit')): ?>
                        <div class="x-list b-none">
                            <button type="button"
                                class="btn btn-rounded-x btn-danger edit-add-modal-button js-ajax-ux-request"
                                data-toggle="modal"
                                data-url="/tickets/<?php echo e($ticket->ticket_id); ?>/edit?edit_type=all&edit_source=leftpanel"
                                data-action-url="/tickets/<?php echo e($ticket->ticket_id); ?>" data-target="#commonModal"
                                data-loading-target="commonModalBody" data-action-method="PUT"
                                data-modal-title="<?php echo e(cleanLang(__('lang.edit_ticket'))); ?>">
                                <?php echo e(cleanLang(__('lang.edit_ticket'))); ?></button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!--options--><?php /**PATH D:\my_apache\application\resources\views/pages/ticket/components/panel.blade.php ENDPATH**/ ?>