 <?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        <?php echo $__env->make('misc.heading-crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        <?php echo $__env->make('pages.tasks.components.misc.list-page-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!--stats panel-->
    <?php if(auth()->user()->is_team): ?>
    <div class="stats-wrapper " id="tasks-stats-wrapper">
        <?php echo $__env->make('pages.tasks.components.misc.list-pages-stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <?php endif; ?>
    <!--stats panel-->


    <!-- page content -->
    <div class="row kanban-wrapper">
        <div class="col-12" id="tasks-layout-wrapper">
            <?php if(auth()->user()->pref_view_tasks_layout == 'kanban'): ?>
            <?php echo $__env->make('pages.tasks.components.kanban.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
            <!--tasks table-->
            <?php echo $__env->make('pages.tasks.components.table.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!--tasks table-->
            <?php endif; ?>
            <!--filter-->
            <?php if(auth()->user()->is_team): ?>
            <?php echo $__env->make('pages.tasks.components.misc.filter-tasks', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
            <!--filter-->
        </div>
    </div>
    <!--page content -->

</div>
<!--main content -->

<!--task modal-->
<?php echo $__env->make('pages.task.modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!--dynamic load task task (dynamic_trigger_dom)-->
<?php if(config('visibility.dynamic_load_modal')): ?>
<a href="javascript:void(0)" id="dynamic-task-content"
    class="show-modal-button reset-card-modal-form js-ajax-ux-request js-ajax-ux-request" data-toggle="modal"
    data-target="#cardModal" data-url="<?php echo e(url('/tasks/'.request()->route('task').'?ref=list')); ?>"
    data-loading-target="main-top-nav-bar"></a>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\my_apache\application\resources\views/pages/tasks/wrapper.blade.php ENDPATH**/ ?>