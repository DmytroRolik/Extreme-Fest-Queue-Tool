<div style="text-align: center; padding-top: 7px">
    @can('users-edit')
        <a data-toggle="tooltip" title="Редактировать" href="{{route('admin_users_edit', $id)}}" class="btn btn-success rules-tooltip">
        <i class="fa fa-pencil" aria-hidden="true"></i>
        </a>

        <button type="submit" data-toggle="tooltip" title="Удалить" class="btn btn-danger">
                <i class="fa fa-trash" aria-hidden="true"></i>
        </button>
    @else
        <a data-toggle="tooltip" title="Просмотреть" href="{{route('admin_users_view', $id)}}" class="btn btn-success rules-tooltip">
        <i class="fa fa-eye" aria-hidden="true"></i>
        </a>
    @endcan
</div>