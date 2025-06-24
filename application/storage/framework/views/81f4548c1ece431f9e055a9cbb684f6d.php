<!-- action buttons -->
<?php echo $__env->make('pages.projects.components.misc.list-page-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<!-- action buttons -->

<!--stats panel-->
<div id="projects-stats-wrapper" class="stats-wrapper card-embed-fix">
<?php if(@count($projects ?? []) > 0 && auth()->user()->is_team): ?> <?php echo $__env->make('misc.list-pages-stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>
</div>
<!--stats panel-->

<!--projects table-->
<div class="card-embed-fix">
<?php echo $__env->make('pages.projects.views.list.table.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<!--projects table--><?php /**PATH D:\my_apache\application\resources\views/pages/projects/views/list/tabswrapper.blade.php ENDPATH**/ ?>