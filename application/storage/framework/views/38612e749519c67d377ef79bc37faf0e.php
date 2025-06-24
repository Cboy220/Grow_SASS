<div class="container py-5">
    <div class="card shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-bullseye text-primary mr-2"></i><?php echo e(__('lang.client_expectations')); ?>

            </h4>
            <button class="btn btn-outline-primary edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                id="" data-toggle="modal" data-target="#basicModal"
                data-url="<?php echo e(url('/expectation/create/' . $client->clientid)); ?>" data-loading-target="basicModal">
                <i class="fas fa-plus mr-1"></i> <?php echo e(__('lang.add_expectation')); ?>

            </button>
        </div>

        <!-- Search -->
        <!-- Search bar -->
        <div class="input-group mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="<?php echo e(__('lang.search_expectations_placeholder')); ?>">
            <div class="input-group-append">
                <button id="expectationSearchBtn" data-action-url="<?php echo e(route('expectation.fetch', $client->clientid)); ?>"
                    class="btn btn-outline-primary">
                    <i class="fas fa-search"></i> <?php echo e(__('lang.search')); ?>

                </button>
            </div>
        </div>

        <!-- Container for progress bar + list -->
        <div id="expectationStatsContainer"></div>
        <div id="expectationListContainer"></div>
    </div>
</div>
<?php /**PATH D:\my_apache\application\resources\views/pages/client/components/tabs/expectativas.blade.php ENDPATH**/ ?>