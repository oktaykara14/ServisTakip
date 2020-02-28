@if(Auth::user())
<button type="button" class="quick-sidebar-toggler" data-toggle="collapse">
    <span class="sr-only">Toggle Quick Sidebar</span>
    <i class="icon-logout"></i>
    <div class="quick-sidebar-notification">
        <span class="badge badge-danger">7</span>
    </div>
</button>
@endif