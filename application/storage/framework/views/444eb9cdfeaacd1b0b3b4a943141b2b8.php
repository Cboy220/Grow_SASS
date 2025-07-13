
<?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid <?php echo e($document->doc_type); ?>">

    <!--[proposal] heading-->
    <?php if($document->doc_type == 'proposal'): ?>
    <div class="row page-titles">
        <?php echo $__env->make('pages.documents.components.proposal.crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if(auth()->user()->is_team): ?>
        <?php echo $__env->make('pages.documents.components.proposal.actions-team', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
        <?php echo $__env->make('pages.documents.components.proposal.actions-client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!--[proposal] heading-->
    <?php if($document->doc_type == 'contract'): ?>
    <div class="row page-titles">
        <?php echo $__env->make('pages.documents.components.contract.crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if(auth()->user()->is_team): ?>
        <?php echo $__env->make('pages.documents.components.contract.actions-team', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
        <?php echo $__env->make('pages.documents.components.contract.actions-client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!--container-->
    <div class="row" id="embed-content-container">

        <?php echo $__env->yieldContent('document'); ?>

    </div>

</div>
<!--page content -->
</div>

<!--boot js-->
<script src="public/js/core/docs.js?v=<?php echo e(config('system.versioning')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Grow_SaaS\application\resources\views/pages/documents/wrapper-auth.blade.php ENDPATH**/ ?>