<?php $__currentLoopData = $queries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $query): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr data-id="<?php echo e($query->feedback_query_id); ?>">
        <td><?php echo e($query->feedback_query_id); ?></td>
        <td class="static-title"><?php echo e($query->title); ?></td>
        <td class="static-content"><?php echo e($query->content); ?></td>
        <td class="static-type" data-value="<?php echo e($query->type); ?>">
            <?php
                $type = '1';
                switch ($query->type) {
                    case '1':
                        $type='Numeric';
                        break;
                    case '2':
                        $type='Star';  
                        break;
                    case '3':
                        $type='Selector';
                        break;

                    default:
                        $type='Numeric';
                }
            ?>
            <?php echo e($type); ?>

        </td>
        <td class="static-range"><?php echo e($query->range); ?></td>
        <td class="static-weight"><?php echo e(number_format($query->weight, 1)); ?></td>
        <td>
            <button class="btn btn-sm btn-primary edit-query"
                data-action-url="<?php echo e(route('settings.feedback.edit', $query->feedback_query_id)); ?>"
                data-id="<?php echo e($query->feedback_query_id); ?>">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger delete-query"
                data-action-url="<?php echo e(route('settings.feedback.delete', $query->feedback_query_id)); ?>"
                data-id="<?php echo e($query->feedback_query_id); ?>">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php if($queries->isEmpty()): ?>
    <tr>
        <td colspan="7" class="text-center text-muted">No queries found.</td>
    </tr>
<?php endif; ?>
<?php /**PATH D:\my_apache\application\resources\views/pages/settings/sections/feedback/components/table/tbody-ajax.blade.php ENDPATH**/ ?>