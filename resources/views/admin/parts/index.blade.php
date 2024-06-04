@extends('layouts.admin')
@section('content')
@can('part_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.parts.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.part.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.part.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Part">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.part.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.part.fields.photo') }}
                        </th>
                        <th>
                            {{ trans('cruds.part.fields.data') }}
                        </th>
                        <th>
                            {{ trans('cruds.part.fields.exist') }}
                        </th>
                        <th>
                            {{ trans('cruds.part.fields.product_info') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parts as $key => $part)
                        <tr data-entry-id="{{ $part->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $part->id ?? '' }}
                            </td>
                            <td>
                                @if($part->photo)
                                    <a href="{{ $part->photo->getUrl() }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $part->photo->getUrl('thumb') }}">
                                    </a>
                                @endif
                            </td>
                            <td>
                                {{ $part->data ?? '' }}
                            </td>
                            <td>
                                {{ App\Models\Part::EXIST_RADIO[$part->exist] ?? '' }}
                            </td>
                            <td>
                                {{ $part->product_info ?? '' }}
                            </td>
                            <td>
                                @can('part_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.parts.show', $part->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('part_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.parts.edit', $part->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('part_delete')
                                    <form action="{{ route('admin.parts.destroy', $part->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('part_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.parts.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Part:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection