<a style="text-decoration: underline; cursor: default" class="rules-tooltip">

    @if(count($account->permissions()->toArray()) || count($account->activities_permissions()->toArray()))
        <div class="rules-tooltip-text">
            @foreach($account->permissions() as $permissions)
                {{$permissions->permission_name()}}<br/>
            @endforeach

            @foreach($account->activities_permissions() as $permissions)
                {{$permissions->activity_name()}}<br/>
            @endforeach
        </div>
    @endif
    Права
</a>