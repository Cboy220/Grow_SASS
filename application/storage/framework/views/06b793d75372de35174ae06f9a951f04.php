<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar" id="js-trigger-nav-team">
    <!--[fix] keep id as "js-trigger-nav-team"-->
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar" id="main-scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul data-modular-id="main_menu_client" id="sidebarnav">

                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent1'); ?>


                <!--home-->
                <li data-modular-id="main_menu_client_home"
                    class="sidenav-menu-item <?php echo e($page['mainmenu_home'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.home'))); ?>">
                    <a class="waves-effect waves-dark" href="/home" aria-expanded="false" target="_self">
                        <i class="ti-home"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.dashboard'))); ?>

                        </span>
                    </a>
                </li>
                <!--home-->

                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent2'); ?>


                <!--projects[home]-->
                <?php if(config('visibility.modules.projects')): ?>
                <li data-modular-id="main_menu_client_projects"
                    class="sidenav-menu-item <?php echo e($page['mainmenu_projects'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.projects'))); ?>">
                    <a class="waves-effect waves-dark" href="<?php echo e(_url('/projects')); ?>" aria-expanded="false"
                        target="_self">
                        <i class="ti-folder"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.projects'))); ?>

                        </span>
                    </a>
                </li>
                <?php endif; ?>
                <!--projects-->


                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent3'); ?>


                <?php if(auth()->user()->is_client_owner): ?>
                <li data-modular-id="main_menu_client_billing"
                    class="sidenav-menu-item <?php echo e($page['mainmenu_client_billing'] ?? ''); ?>">
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0);" aria-expanded="false">
                        <i class="ti-wallet"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.billing'))); ?>

                        </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <?php if(config('visibility.modules.invoices')): ?>
                        <li class="sidenav-submenu <?php echo e($page['submenu_invoices'] ?? ''); ?>" id="submenu_invoices">
                            <a href="/invoices"
                                class=" <?php echo e($page['submenu_invoices'] ?? ''); ?>"><?php echo e(cleanLang(__('lang.invoices'))); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if(config('visibility.modules.payments')): ?>
                        <li class="sidenav-submenu <?php echo e($page['submenu_payments'] ?? ''); ?>" id="submenu_payments">
                            <a href="/payments"
                                class=" <?php echo e($page['submenu_payments'] ?? ''); ?>"><?php echo e(cleanLang(__('lang.payments'))); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if(config('visibility.modules.estimates')): ?>
                        <li class="sidenav-submenu <?php echo e($page['submenu_estimates'] ?? ''); ?>" id="submenu_estimates">
                            <a href="/estimates"
                                class=" <?php echo e($page['submenu_estimates'] ?? ''); ?>"><?php echo e(cleanLang(__('lang.estimates'))); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if(config('visibility.modules.subscriptions')): ?>
                        <li class="sidenav-submenu <?php echo e($page['submenu_subscriptions'] ?? ''); ?>"
                            id="submenu_subscriptions">
                            <a href="/subscriptions"
                                class=" <?php echo e($page['submenu_subscriptions'] ?? ''); ?>"><?php echo e(cleanLang(__('lang.subscriptions'))); ?></a>
                        </li>
                        <?php endif; ?>
                        <!--[MODULES] - dynamic menu-->
                        <?php echo config('modules.menus.client.billing'); ?>

                    </ul>
                </li>
                <?php endif; ?>
                                            
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent4'); ?>

            
                <!--proposals-->
                <?php if(config('visibility.modules.proposals') && auth()->user()->is_client_owner): ?>
                <li data-modular-id="main_menu_client_proposals"
                    class="sidenav-menu-proposals <?php echo e($page['mainmenu_client_proposals'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.proposals'))); ?>">
                    <a class="waves-effect waves-dark p-r-20" href="/proposals" aria-expanded="false" target="_self">
                        <i class="ti-bookmark-alt"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.proposals'))); ?>

                        </span>
                    </a>
                </li>
                <?php endif; ?>

                                
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent5'); ?>


                <!--contracts-->
                <?php if(config('visibility.modules.contracts') && auth()->user()->is_client_owner): ?>
                <li data-modular-id="main_menu_client_contracts"
                    class="sidenav-menu-item <?php echo e($page['mainmenu_contracts'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.contracts'))); ?>">
                    <a class="waves-effect waves-dark p-r-20" href="/contracts" aria-expanded="false" target="_self">
                        <i class="ti-write"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.contracts'))); ?>

                        </span>
                    </a>
                </li>
                <?php endif; ?>


                                                
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent5'); ?>


                <!--users-->
                <?php if(auth()->user()->is_client_owner): ?>
                <li data-modular-id="main_menu_client_users"
                    class="sidenav-menu-item <?php echo e($page['mainmenu_contacts'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.users'))); ?>">
                    <a class="waves-effect waves-dark" href="/users" aria-expanded="false" target="_self">
                        <i class="sl-icon-people"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.users'))); ?>

                        </span>
                    </a>
                </li>
                <?php endif; ?>
                <!--users-->

                                                
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent7'); ?>


                <!--tickets-->
                <?php if(config('visibility.modules.tickets')): ?>
                <li data-modular-id="main_menu_client_tickets"
                    class="sidenav-menu-item <?php echo e($page['mainmenu_tickets'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.support_tickets'))); ?>">
                    <a class="waves-effect waves-dark" href="/tickets" aria-expanded="false" target="_self">
                        <i class="ti-comments"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.support'))); ?>

                        </span>
                    </a>
                </li>
                <?php endif; ?>
                <!--tickets-->

                                                
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent8'); ?>


                <!--knowledgebase-->
                <?php if(config('visibility.modules.knowledgebase')): ?>
                <li data-modular-id="main_menu_client_knowledgebase"
                    class="sidenav-menu-item <?php echo e($page['mainmenu_kb'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.knowledgebase'))); ?>">
                    <a class="waves-effect waves-dark p-r-20" href="/knowledgebase" aria-expanded="false"
                        target="_self">
                        <i class="sl-icon-docs"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.knowledgebase'))); ?>

                        </span>
                    </a>
                </li>
                <?php endif; ?>
                <!--knowledgebase-->
                
                <!--feedback-->
                <?php if((int) auth()->user()->role->role_feedback >= 2): ?>
                <li data-modular-id="main_menu_client_feedback"
                    class="sidenav-menu-item <?php echo e($page['mainmenu_feedback'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.feedback'))); ?>">
                    <a class="waves-effect waves-dark p-r-20" href="/feedback" aria-expanded="false"
                        target="_self">
                        <i class="fas fa-star-half-stroke"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.feedback'))); ?>

                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if((int) auth()->user()->role->role_expectation >= 1): ?>
                <li data-modular-id="main_menu_client_expectation"
                    class="sidenav-menu-item <?php echo e($page['mainmenu_expectation'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.expectation'))); ?>">
                    <a class="waves-effect waves-dark p-r-20" href="/expectation" aria-expanded="false"
                        target="_self">
                        <i class="fa-solid fa-chart-simple"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.expectativas'))); ?>

                        </span>
                    </a>
                </li>
                <?php endif; ?>
                <!--feedback-->
                                
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent9'); ?>


                                                
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent10'); ?>


                                                
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent11'); ?>


                                                
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent12'); ?>


                                                
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent13'); ?>


                                                
                <!--[MODULES] - dynamic menu-->
                <?php echo config('modules.menus.main.parent14'); ?>


            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside><?php /**PATH D:\my_apache\application\resources\views/nav/leftmenu-client.blade.php ENDPATH**/ ?>