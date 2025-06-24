<div class="signup-form p-t-30" id="signup-form">

    <div class="x-heading">
        <h4><?php echo e($section->frontend_data_1); ?></h4>
    </div>
    <div class="x-sub-heading">
        <?php echo e($section->frontend_data_2); ?>

    </div>

    <form class="form-horizontal form-material x-form " id="loginForm" novalidate="novalidate" _lpchecked="1">
        <div class="form-group m-b-30">
            <div class="col-xs-12">
                <input class="form-control" type="text" name="full_name" id="full_name" placeholder="<?php echo app('translator')->get('lang.name'); ?>"
                    autocomplete="off" style="cursor: auto;">
            </div>
        </div>
        <div class="form-group m-b-30">
            <div class="col-xs-12">
                <input class="form-control" type="text" name="email_address" id="email_address"
                    placeholder="<?php echo app('translator')->get('lang.email'); ?>" autocomplete="off" style="cursor: auto;">
            </div>
        </div>
        <div class="form-group  m-b-30">
            <div class="col-xs-12">
                <input class="form-control" type="password" name="password" id="password"
                    placeholder="<?php echo app('translator')->get('lang.password'); ?>" autocomplete="off" style="cursor: auto;">
            </div>
        </div>

        <div class="input-group m-b-30">
            <input type="text" class="form-control" name="account_name" placeholder="<?php echo e($section->frontend_data_4); ?>"
                aria-describedby="basic-addon2">
            <span class="input-group-addon x-sub-domain"
                id="basic-addon2">.<?php echo e(config('system.settings_base_domain')); ?></span>
        </div>

        <!--item-->
        <div class="form-group row m-b-30">
            <div class="col-sm-12">
                <select class="select2-basic form-control form-control-sm x-packages-list select2-preselected" id="plan"
                    name="plan" data-preselected="<?php echo e(request('ref') ?? ''); ?>">
                    <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($package->package_subscription_options == 'paid'): ?>
                    <option data-option="monthly" value="monthly_<?php echo e($package->package_id); ?>">
                        <?php echo e($package->package_name); ?>

                        (<?php echo e(runtimeMoneyFormat($package->package_amount_monthly)); ?> / <?php echo app('translator')->get('lang.month'); ?>)
                    </option>
                    <option data-option="yearly" value="yearly_<?php echo e($package->package_id); ?>">
                        <?php echo e($package->package_name); ?>

                        (<?php echo e(runtimeMoneyFormat($package->package_amount_yearly)); ?> / <?php echo app('translator')->get('lang.year'); ?>)
                    </option>
                    <?php else: ?>
                    <option data-option="free" value="free_<?php echo e($package->package_id); ?>"><?php echo e($package->package_name); ?>

                        (<?php echo app('translator')->get('lang.free'); ?>)</option>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="hidden" name="billing_cycle" id="billing_cycle" value="monthly">
            </div>
        </div>

        <!--DYANMIC TRUSTED CONTENT - No sanitizing required] for this trusted content (Google reCAPTCHA)-->
        <?php if(@config('system.settings_captcha_status') == 'enabled'): ?>
        <div class="m-b-20">
            <?php echo htmlFormSnippet([]); ?>

        </div>
        <?php endif; ?>

        <!--terms-->
        <?php if(config('system.settings_terms_of_service_status') == 'enabled'): ?>
        <div class="form-group form-group-checkbox row terms-of-service-link">
            <div class="col-12 p-t-5">
                <input type="checkbox" id="signup_agree_terms" name="signup_agree_terms"
                    class="filled-in chk-col-light-blue signup_agree_terms">
                <label class="p-l-10" for="signup_agree_terms">
                    <a href="#" data-toggle="modal" data-target="#termsModal">
                        <?php echo e(config('system.settings_terms_of_service_text')); ?>

                    </a>
                </label>
            </div>
        </div>
        <?php endif; ?>

        <div class="form-group text-center p-b-10">
            <div class="col-xs-12">
                <button class="btn btn-info btn-lg btn-block" id="accountSignupButton"
                    data-button-loading-annimation="yes" data-button-disable-on-click="yes"
                    data-url="<?php echo e(url('account/signup')); ?>" data-ajax-type="POST"
                    type="submit"><?php echo e($section->frontend_data_3); ?></button>
            </div>
        </div>
    </form>

</div><?php /**PATH D:\my_apache\application\resources\views/frontend/signup/form.blade.php ENDPATH**/ ?>