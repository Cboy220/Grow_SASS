<!--checkbox actions-->
@include('pages.client.components.actions.checkbox-actions')

<!--main table view-->
@include('pages.client.components.table.table')
<!--filter-->
@if(auth()->user()->is_team)
@include('pages.client.components.misc.filter-projects')
@endif
<!--filter-->