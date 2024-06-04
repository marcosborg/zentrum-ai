@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.part.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.parts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.part.fields.id') }}
                        </th>
                        <td>
                            {{ $part->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.part.fields.photo') }}
                        </th>
                        <td>
                            @if($part->photo)
                                <a href="{{ $part->photo->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $part->photo->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.part.fields.data') }}
                        </th>
                        <td>
                            {{ $part->data }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.part.fields.exist') }}
                        </th>
                        <td>
                            {{ App\Models\Part::EXIST_RADIO[$part->exist] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.part.fields.product_info') }}
                        </th>
                        <td>
                            {{ $part->product_info }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.parts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection