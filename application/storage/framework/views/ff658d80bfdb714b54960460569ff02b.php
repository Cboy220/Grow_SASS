<?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr id="project_<?php echo e($project->project_id); ?>" class="<?php echo e($project->pinned_status ?? ''); ?>">
    <?php if(config('visibility.projects_col_checkboxes')): ?>
    <td class="projects_col_checkbox checkitem" id="projects_col_checkbox_<?php echo e($project->project_id); ?>">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-projects-<?php echo e($project->project_id); ?>"
                name="ids[<?php echo e($project->project_id); ?>]"
                class="listcheckbox listcheckbox-projects filled-in chk-col-light-blue"
                data-actions-container-class="projects-checkbox-actions-container">
            <label for="listcheckbox-projects-<?php echo e($project->project_id); ?>"></label>
        </span>
    </td>
    <?php endif; ?>
    <!--tableconfig_column_1 [project_id]-->
    <td class="projects_col_id <?php echo e(config('table.tableconfig_column_1')); ?> tableconfig_column_1">
        <a href="<?php echo e(_url('/projects/'.$project->project_id)); ?>"><?php echo e($project->project_id); ?></label></a>
    </td>
    <!--tableconfig_column_2 [project_title]-->
    <td class="projects_col_project <?php echo e(config('table.tableconfig_column_2')); ?> tableconfig_column_2">
        <a href="<?php echo e(_url('/projects/'.$project->project_id)); ?>"><?php echo e(str_limit($project->project_title ??'---', 20)); ?><a />
            <!--automation-->
            <?php if(auth()->user()->is_team && $project->project_automation_status == 'enabled'): ?>
            <span class="sl-icon-energy text-warning p-l-5" data-toggle="tooltip"
                title="<?php echo app('translator')->get('lang.project_automation'); ?>"></span>
            <?php endif; ?>
    </td>
    <!--tableconfig_column_3 [client_company_name]-->
    <?php if(config('visibility.projects_col_client')): ?>
    <td class="projects_col_client <?php echo e(config('table.tableconfig_column_3')); ?> tableconfig_column_3">
        <a
            href="/clients/<?php echo e($project->project_clientid); ?>"><?php echo e(str_limit($project->client_company_name ??'---', 12)); ?></a>
    </td>
    <?php endif; ?>
    <!--tableconfig_column_4 [project_date_start]-->
    <td class="projects_col_start_date <?php echo e(config('table.tableconfig_column_4')); ?> tableconfig_column_4">
        <?php echo e(runtimeDate($project->project_date_start)); ?>

    </td>
    <!--tableconfig_column_5 [project_date_due]-->
    <td class="projects_col_end_date <?php echo e(config('table.tableconfig_column_5')); ?> tableconfig_column_5">
        <?php echo e(runtimeDate($project->project_date_due)); ?></td>
    <!--tableconfig_column_6 [tags]-->
    <?php if(config('visibility.projects_col_tags')): ?>
    <td class="projects_col_tags <?php echo e(config('table.tableconfig_column_6')); ?> tableconfig_column_6">
        <!--tag-->
        <?php if(count($project->tags ?? []) > 0): ?>
        <?php $__currentLoopData = $project->tags->take(1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span class="label label-outline-default"><?php echo e(str_limit($tag->tag_title, 15)); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
        <span>---</span>
        <?php endif; ?>
        <!--/#tag-->

        <!--more tags (greater than tags->take(x) number above -->
        <?php if(count($project->tags ?? []) > 1): ?>
        <?php $tags = $project->tags; ?>
        <?php echo $__env->make('misc.more-tags', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
        <!--more tags-->
    </td>
    <?php endif; ?>
    <!--tableconfig_column_7 [project_progress]-->
    <td class="projects_col_progress p-r-20 <?php echo e(config('table.tableconfig_column_7')); ?> tableconfig_column_7">
        <div class="progress" data-toggle="tooltip" title="<?php echo e($project->project_progress); ?>%">
            <?php if($project->project_progress == 100): ?>
            <div class="progress-bar bg-success w-100 h-px-10 font-11 font-weight-500" data-toggle="tooltip"
                title="100%" role="progressbar"></div>
            <?php else: ?>
            <!--dynamic inline style-->
            <div class="progress-bar bg-info h-px-10 font-16 font-weight-500 w-<?php echo e(round($project->project_progress)); ?>"
                role="progressbar"></div>
            <?php endif; ?>
        </div>
        <!--print view-->
        <div class="printing_progress_bar hidden"><?php echo e(round($project->project_progress)); ?>%</div>
    </td>

    <!--tableconfig_column_8 [count_pending_tasks]-->
    <td class="col_count_pending_tasks <?php echo e(config('table.tableconfig_column_8')); ?> tableconfig_column_8">
        <?php echo e($project->count_pending_tasks); ?>

    </td>

    <!--tableconfig_column_9 [count_tasks_completed]-->
    <td class="col_count_completed_tasks <?php echo e(config('table.tableconfig_column_9')); ?> tableconfig_column_9">
        <?php echo e($project->count_completed_tasks); ?>

    </td>

    <!--tableconfig_column_10 [sum_invoices_all]-->

    <td class="col_sum_invoices_all <?php echo e(config('table.tableconfig_column_10')); ?> tableconfig_column_10">
        <?php echo e(runtimeMoneyFormat($project->sum_invoices_all)); ?>

    </td>

    <!--tableconfig_column_11 [sum_all_payments]-->
    <td class="col_sum_all_payments <?php echo e(config('table.tableconfig_column_11')); ?> tableconfig_column_11">
        <?php echo e(runtimeMoneyFormat($project->sum_all_payments)); ?>

    </td>

    <!--tableconfig_column_12 [sum_outstanding_balance]-->
    <?php if(config('visibility.role_invoices')): ?>
    <td class="col_sum_outstanding_balance <?php echo e(config('table.tableconfig_column_12')); ?> tableconfig_column_12">
        <?php echo e(runtimeMoneyFormat($project->sum_outstanding_balance)); ?>

    </td>
    <?php endif; ?>

    <!--tableconfig_column_13 [project_billing_type]-->
    <?php if(config('visibility.role_invoices')): ?>
    <td class="col_project_billing_type <?php echo e(config('table.tableconfig_column_13')); ?> tableconfig_column_13">
        <?php echo e(runtimeLang($project->project_billing_type)); ?>

    </td>
    <?php endif; ?>

    <!--tableconfig_column_14 [project_billing_estimated_hours]-->
    <?php if(config('visibility.role_estimates')): ?>
    <td class="col_project_billing_estimated_hours <?php echo e(config('table.tableconfig_column_14')); ?> tableconfig_column_14">
        <?php echo e($project->project_billing_estimated_hours); ?>

    </td>
    <?php endif; ?>

    <!--tableconfig_column_15 [project_billing_costs_estimate]-->
    <?php if(config('visibility.role_estimates')): ?>
    <td class="col_project_billing_costs_estimate <?php echo e(config('table.tableconfig_column_15')); ?> tableconfig_column_15">
        <?php echo e(runtimeMoneyFormat($project->project_billing_costs_estimate)); ?>

    </td>
    <?php endif; ?>

    <!--tableconfig_column_16 [sum_hours]-->
    <?php if(config('visibility.role_timesheets')): ?>
    <td class="col_sum_hours <?php echo e(config('table.tableconfig_column_16')); ?> tableconfig_column_16">
        <?php echo e(runtimeSecondsWholeHours($project->sum_hours)); ?>:<?php echo e(runtimeSecondsWholeMinutesZero($project->sum_hours)); ?>

    </td>
    <?php endif; ?>

    <!--tableconfig_column_17 [sum_expenses]-->
    <?php if(config('visibility.role_expenses')): ?>
    <td class="col_sum_expenses <?php echo e(config('table.tableconfig_column_17')); ?> tableconfig_column_17">
        <?php echo e(runtimeMoneyFormat($project->sum_expenses)); ?>

    </td>
    <?php endif; ?>

    <!--tableconfig_column_18 [count_files]-->
    <td class="col_count_files <?php echo e(config('table.tableconfig_column_18')); ?> tableconfig_column_18">
        <?php echo e($project->count_files); ?>

    </td>

    <!--tableconfig_column_19 [count_tickets_open]-->
    <td class="count_tickets_open <?php echo e(config('table.tableconfig_column_19')); ?> tableconfig_column_19">
        <?php echo e($project->count_tickets_open); ?>

    </td>

    <!--tableconfig_column_20 [count_tickets_closed]-->
    <td class="col_count_tickets_closed <?php echo e(config('table.tableconfig_column_20')); ?> tableconfig_column_20">
        <?php echo e($project->count_tickets_closed); ?>

    </td>


    <!--tableconfig_column_21 [assigned]-->
    <?php if(config('visibility.projects_col_team')): ?>
    <td
        class="projects_col_team column_assigned_users <?php echo e(config('table.tableconfig_column_21')); ?> tableconfig_column_21">
        <!--assigned users-->
        <?php if(count($project->assigned ?? []) > 0): ?>
        <?php $__currentLoopData = $project->assigned->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <img src="<?php echo e($user->avatar); ?>" data-toggle="tooltip" title="<?php echo e($user->first_name); ?>" data-placement="top"
            alt="<?php echo e($user->first_name); ?>" class="img-circle avatar-xsmall">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
        <span>---</span>
        <?php endif; ?>
        <!--assigned users-->
        <!--more users-->
        <?php if(count($project->assigned ?? []) > 2): ?>
        <?php $more_users_title = __('lang.assigned_users'); $users = $project->assigned; ?>
        <?php echo $__env->make('misc.more-users', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
        <!--more users-->
    </td>
    <?php endif; ?>
    <!--tableconfig_column_22 [project_status]-->
    <td class="projects_col_status <?php echo e(config('table.tableconfig_column_22')); ?> tableconfig_column_22">
        <span
            class="label <?php echo e(runtimeProjectStatusColors($project->project_status, 'label')); ?>"><?php echo e(runtimeLang($project->project_status)); ?></span>
        <!--archived-->
        <?php if($project->project_active_state == 'archived' && runtimeArchivingOptions()): ?>
        <span class="label label-icons label-icons-default" data-toggle="tooltip" data-placement="top"
            title="<?php echo app('translator')->get('lang.archived'); ?>"><i class="ti-archive"></i></span>
        <?php endif; ?>
    </td>
    <?php if(config('visibility.projects_col_actions')): ?>
    <td class="projects_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <div class="dropdown-menu" aria-labelledby="quick-access-button">
                <!--recurring settings-->
                <a class="dropdown-item js-toggle-side-panel js-quick-access-button" href="javascript:void(0)"
                    data-title="<?php echo app('translator')->get('lang.project_files'); ?>" data-target="quick-access-sidepanel"
                    data-url="<?php echo e(url('/projects/'.$project->project_id.'/quick-access/files')); ?>"
                    data-loading-target="commonModalBody"
                    data-action-ajax-loading-target="invoices-td-container"><?php echo app('translator')->get('lang.files'); ?></a>
            </div>


            <?php if(config('visibility.action_buttons_delete')): ?>
            <!--[delete]-->
            <?php if($project->permission_delete_project): ?>
            <button type="button" title="<?php echo e(cleanLang(__('lang.delete'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="<?php echo e(cleanLang(__('lang.delete_item'))); ?>"
                data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>" data-ajax-type="DELETE"
                data-url="<?php echo e(_url('/projects/'.$project->project_id)); ?>">
                <i class="sl-icon-trash"></i>
            </button>
            <?php else: ?>
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled  <?php echo e(runtimePlaceholdeActionsButtons()); ?>"
                data-toggle="tooltip" title="<?php echo e(cleanLang(__('lang.actions_not_available'))); ?>"><i
                    class="sl-icon-trash"></i></span>
            <?php endif; ?>
            <?php endif; ?>
            <!--[edit]-->
            <?php if(config('visibility.action_buttons_edit')): ?>
            <?php if($project->permission_edit_project): ?>
            <button type="button" title="<?php echo e(cleanLang(__('lang.edit'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="<?php echo e(urlResource('/projects/'.$project->project_id.'/edit')); ?>"
                data-loading-target="commonModalBody" data-modal-title="<?php echo e(cleanLang(__('lang.edit_project'))); ?>"
                data-action-url="<?php echo e(urlResource('/projects/'.$project->project_id)); ?>" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="projects-td-container">
                <i class="sl-icon-note"></i>
            </button>
            <?php else: ?>
            <!--optionally show disabled button?-->
            <span class="btn btn-outline-default btn-circle btn-sm disabled  <?php echo e(runtimePlaceholdeActionsButtons()); ?>"
                data-toggle="tooltip" title="<?php echo e(cleanLang(__('lang.actions_not_available'))); ?>"><i
                    class="sl-icon-note"></i></span>
            <?php endif; ?>
            <?php if(auth()->user()->role->role_assign_projects == 'yes'): ?>
            <button type="button" title="<?php echo e(cleanLang(__('lang.assigned_users'))); ?>"
                class="btn btn-outline-info btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form data-toggle-action-tooltip"
                data-toggle="modal" data-target="#commonModal"
                data-url="<?php echo e(urlResource('/projects/'.$project->project_id.'/assigned')); ?>"
                data-loading-target="commonModalBody" data-modal-title="<?php echo e(cleanLang(__('lang.assigned_users'))); ?>"
                data-action-url="<?php echo e(urlResource('/projects/'.$project->project_id.'/assigned')); ?>"
                data-action-method="PUT" data-modal-size="modal-sm" data-action-ajax-class="ajax-request"
                data-action-ajax-class="" data-action-ajax-loading-target="projects-td-container">
                <i class="sl-icon-people"></i>
            </button>
            <?php endif; ?>
            <?php endif; ?>
        </span>
        <!--action button-->
        <!--more button (team)-->
        <?php if(config('visibility.action_buttons_edit')): ?>
        <span class="list-table-action dropdown font-size-inherit">
            <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                title="<?php echo e(cleanLang(__('lang.more'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm">
                <i class="ti-more"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="listTableAction">
                <?php echo $__env->make('pages.projects.views.common.dropdown-menu-team', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </span>
        <?php endif; ?>

        <!--pin-->
        <span class="list-table-action">
            <a href="javascript:void(0);" title="<?php echo e(cleanLang(__('lang.pinning'))); ?>"
                data-parent="project_<?php echo e($project->project_id); ?>"
                data-url="<?php echo e(url('/projects/'.$project->project_id.'/pinning')); ?>"
                class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm opacity-4 js-toggle-pinning">
                <i class="ti-pin2"></i>
            </a>
        </span>
    </td>
    <?php endif; ?>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<!--each row--><?php /**PATH D:\my_apache\application\resources\views/pages/projects/views/list/table/ajax.blade.php ENDPATH**/ ?>