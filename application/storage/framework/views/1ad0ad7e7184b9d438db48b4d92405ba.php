<div class="card m-t--50">
    <div class="card-body">

        <!--edit user icon-->
        <?php if(auth()->user()->is_admin): ?>
        <div class="text-right p-b-10">
            <button type="submit" id="submitButton"
                class="btn btn-xxs btn-outline-info waves-effect text-left edit-add-modal-button ajax-request"
                data-action-url="<?php echo e(url('contacts/'.$contact->id.'?ref=profile-modal')); ?>"
                data-skip-modal-body-reset="yes" data-modal-title="<?php echo app('translator')->get('lang.edit_profile'); ?>" data-action-method="PUT"
                data-url="<?php echo e(url('contacts/'.$contact->id.'/edit?ref=list')); ?>"><?php echo app('translator')->get('lang.edit_profile'); ?></button>
        </div>
        <?php endif; ?>

        <center> <img src="<?php echo e(getUsersAvatar($contact->avatar_directory, $contact->avatar_filename)); ?>"
                class="img-circle" width="120">
            <h4 class="card-title m-t-10"><?php echo e($contact->first_name); ?> <?php echo e($contact->last_name); ?></h4>
            <h6 class="card-subtitle"><?php echo e($contact->email); ?></h6>
            <span class="label <?php echo e(runtimeUserTypeLabel($contact->type)); ?>">
                <?php if($contact->type == 'team'): ?>
                <?php echo app('translator')->get('lang.team_member'); ?>
                <?php endif; ?>
                <?php if($contact->type == 'client'): ?>
                <?php echo app('translator')->get('lang.client_user'); ?>
                <?php endif; ?>
                <?php if($contact->type == 'contact'): ?>
                <?php echo app('translator')->get('lang.email_contact'); ?>
                <?php endif; ?>
            </span>
        </center>
    </div>
    <div>
        <hr>
    </div>
    <div class="card-body p-t-0">
        <small class="text-muted p-t-10 db"><?php echo app('translator')->get('lang.telephone'); ?></small>
        <h6><?php echo e($contact->phone ?? '---'); ?></h6>
        <small class="text-muted p-t-30 db"><?php echo app('translator')->get('lang.job_title'); ?></small>
        <h6><?php echo e($contact->position ?? '---'); ?></h6>
        <small class="text-muted p-t-30 db"><?php echo app('translator')->get('lang.date_added'); ?></small>
        <h6><?php echo e(runtimeDate($contact->created)); ?></h6>
        <small class="text-muted p-t-30 db"><?php echo app('translator')->get('lang.last_seen'); ?></small>
        <h6><?php echo e(runtimeDateAgo($contact->last_seen)); ?></h6>
        <br>
        <?php if($contact->social_twitter != ''): ?>
        <a class="btn btn-circle btn-secondary" href="https://x.com/<?php echo e($contact->social_twitter); ?>"><i
                class="sl-icon-social-twitter"></i></a>
        <?php endif; ?>
        <?php if($contact->social_linkedin != ''): ?>
        <a class="btn btn-circle btn-secondary" href="https://x.com/<?php echo e($contact->social_linkedin); ?>"><i
                class="sl-icon-social-linkedin"></i></a>
        <?php endif; ?>
        <?php if($contact->social_facebook != ''): ?>
        <a class="btn btn-circle btn-secondary" href="https://x.com/<?php echo e($contact->social_facebook); ?>"><i
                class="sl-icon-social-facebook"></i></a>
        <?php endif; ?>
        <?php if($contact->social_github != ''): ?>
        <a class="btn btn-circle btn-secondary" href="https://x.com/<?php echo e($contact->social_github); ?>"><i
                class="sl-icon-social-github"></i></a>
        <?php endif; ?>
    </div>
</div><?php /**PATH D:\my_apache\application\resources\views/pages/contacts/components/modals/show.blade.php ENDPATH**/ ?>