<!-- Page Title & Bread Crumbs -->
<div class="col-md-12 col-lg-6 align-self-center">
    <!--attached to project-->
    <?php if(is_numeric($document->doc_project_id)): ?>
    <a id="InvoiceTitleAttached" href="<?php echo e(_url('projects/'.$document->bill_projectid)); ?>">
        <h3 class="text-themecolor"><?php echo e($document->project_title); ?></h3>
    </a>
    <?php else: ?>
    <!--not attached to project-->
    <h4 class="muted"><?php echo e(cleanLang(__('lang.not_attached_to_project'))); ?></h4>
    <?php endif; ?>

    <!--crumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><?php echo e(cleanLang(__('lang.app'))); ?></li>
        <?php if(isset($page['crumbs'])): ?>
        <?php $__currentLoopData = $page['crumbs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="breadcrumb-item <?php if($loop->last): ?> active <?php endif; ?>"><?php echo e($title ?? ''); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </ol>
    <!--crumbs-->
</div>
<!--Page Title & Bread Crumbs --><?php /**PATH D:\Grow_SaaS\application\resources\views/pages/documents/components/contract/crumbs.blade.php ENDPATH**/ ?>