@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.part.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.parts.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="photo">{{ trans('cruds.part.fields.photo') }}</label>
                <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}" id="photo-dropzone">
                </div>
                @if($errors->has('photo'))
                    <div class="invalid-feedback">
                        {{ $errors->first('photo') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.part.fields.photo_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="data">{{ trans('cruds.part.fields.data') }}</label>
                <textarea class="form-control {{ $errors->has('data') ? 'is-invalid' : '' }}" name="data" id="data">{{ old('data') }}</textarea>
                @if($errors->has('data'))
                    <div class="invalid-feedback">
                        {{ $errors->first('data') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.part.fields.data_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.part.fields.exist') }}</label>
                @foreach(App\Models\Part::EXIST_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('exist') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="exist_{{ $key }}" name="exist" value="{{ $key }}" {{ old('exist', '') === (string) $key ? 'checked' : '' }} required>
                        <label class="form-check-label" for="exist_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('exist'))
                    <div class="invalid-feedback">
                        {{ $errors->first('exist') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.part.fields.exist_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="product_info">{{ trans('cruds.part.fields.product_info') }}</label>
                <textarea class="form-control {{ $errors->has('product_info') ? 'is-invalid' : '' }}" name="product_info" id="product_info">{{ old('product_info') }}</textarea>
                @if($errors->has('product_info'))
                    <div class="invalid-feedback">
                        {{ $errors->first('product_info') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.part.fields.product_info_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    Dropzone.options.photoDropzone = {
    url: '{{ route('admin.parts.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="photo"]').remove()
      $('form').append('<input type="hidden" name="photo" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="photo"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($part) && $part->photo)
      var file = {!! json_encode($part->photo) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="photo" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}

</script>
@endsection