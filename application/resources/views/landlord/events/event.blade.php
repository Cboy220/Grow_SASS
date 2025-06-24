@foreach($events as $event)
<div class="profiletimeline">
    <div class="sl-item">
        <div class="sl-left">
            @if($event->event_creator_type =='customer')
            <img src="{{ url('/storage/avatars/system/default_avatar.jpg') }}" alt="user" class="img-circle" />
            @endif
            @if($event->event_creator_type =='admin')
            <img src="{{ getUsersAvatar($event->avatar_directory, $event->avatar_filename, $event->event_creatorid)  }}"
                alt="user" class="img-circle" />
            @endif
            @if($event->event_creator_type =='system')
            <img src="{{ url('/storage/avatars/system/avatar.jpg') }}" alt="user" class="img-circle" />
            @endif
        </div>
        <div class="sl-right">
            <div>
                @if($event->event_creator_type =='customer')
                {{ $event->tenant_name }}
                @endif
                @if($event->event_creator_type =='admin')
                {{ $event->first_name }}
                @endif
                @if($event->event_creator_type =='system')
                @lang('lang.system')
                @endif
                <span class="sl-date">{{ runtimeDateAgo($event->event_created) }}</span>
                <!--new account created-->
                @if($event->event_type == 'account-created')
                @include('landlord.events.components.account_created')
                @endif
                <!--updated account-->
                @if($event->event_type == 'account-updated')
                @include('landlord.events.components.account_updated')
                @endif
                <!--changed plan-->
                @if($event->event_type == 'changed-plan')
                @include('landlord.events.components.changed_plan')
                @endif
                <!--created a subscription-->
                @if($event->event_type == 'created-subscription')
                @include('landlord.events.components.created_subscription')
                @endif
                <!--cancelled subscription-->
                @if($event->event_type == 'subscription-cancelled')
                @include('landlord.events.components.subscription_cancelled')
                @endif
                <!--paid subscription-->
                @if($event->event_type == 'subscription-paid')
                @include('landlord.events.components.subscription_paid')
                @endif
                <!--updated password-->
                @if($event->event_type == 'password-updated')
                @include('landlord.events.components.password_updated')
                @endif
                <!--synced account-->
                @if($event->event_type == 'account-synced')
                @include('landlord.events.components.account_synced')
                @endif
            </div>
        </div>
    </div>
    <hr>
</div>
@endforeach