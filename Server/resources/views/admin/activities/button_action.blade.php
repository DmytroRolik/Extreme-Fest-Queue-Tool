<div style="text-align: center; padding-top: 7px">
@can('activities-edit')
        <form method="post" action="{{route("admin_activities_delete")}}">
        @csrf
        <a data-toggle="tooltip" title="Редактировать" href="{{route('admin_activities_edit', $id)}}" class="btn btn-success">
        <i class="fa fa-pencil" aria-hidden="true"></i>
    </a>
        <button type="submit" data-toggle="tooltip" title="Удалить" class="btn btn-danger">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </button>
        <input type="hidden" name="for-delete[]" value="{{$id}}">
    </form>
@else
    <a data-toggle="tooltip" title="Просмотреть" href="{{route('admin_activities_view', $id)}}" class="btn btn-success">
        <i class="fa fa-eye" aria-hidden="true"></i>
    </a>
@endcan
</div>