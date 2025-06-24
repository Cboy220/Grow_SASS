@foreach($pages as $page)
<!--each row-->
<tr id="pages_{{ $page->page_id }}">

    <!--page_title-->
    <td class="col_page_title">
        <a
            href="{{ url('app-admin/frontend/pages/'.$page->page_id.'/edit') }}">{{ str_limit($page->page_title ?? '---', 100) }}</a>
    </td>

    <!--creator-->
    <td class="col_creator">
        <img src="{{ getUsersAvatar($page->avatar_directory, $page->avatar_filename, $page->page_creatorid) }}"
            alt="user" class="img-circle avatar-xsmall">
        {{ checkUsersName($page->first_name, $page->page_creatorid)  }}
    </td>

    <!--page_created-->
    <td class="col_page_created">
        {{ runtimeDate($page->page_created) }}
    </td>

    <!--page_status-->
    <td class="col_page_status">
        @if($page->page_status == 'published')
        <span class="label label-outline-info">@lang('lang.published')</span>
        @else
        <span class="label label-outline-default">@lang('lang.draft')</span>
        @endif
    </td>

    <!--actions-->
    <td class="pages_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('app-admin/frontend/pages/'.$page->page_id) }}">
                <i class="sl-icon-trash"></i>
            </button>
            <!--edit-->
            <a href="{{ url('app-admin/frontend/pages/'.$page->page_id.'/edit') }}"
                title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm">
                <i class="sl-icon-note"></i>
            </a>

            <!--view-->
            <a href="https://{{ config('system.settings_frontend_domain').'/page/'.$page->page_permanent_link.'?preview='.$page->page_uniqueid }}"
                title="{{ cleanLang(__('lang.view')) }}" target="_blank"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="ti-new-window"></i>
            </a>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->