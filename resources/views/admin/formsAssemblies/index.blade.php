@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.formsAssembly.title') }}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs">
            @foreach ($projects as $project)
            <li class="nav-item">
                <a class="nav-link {{ $project->id == $project_id ? 'active' : '' }}"
                    href="/admin/forms-assemblies/change-project-id/{{ $project->id }}">{{ $project->name }}</a>
            </li>
            @endforeach
        </ul>
        <div class="row">
            <div class="col-md-4">
                <div class="card mt-4">
                    <div class="card-body">
                        @if ($forms->count() == 0)
                        <div class="alert alert-primary" role="alert">
                            Ainda não existem formulários.
                        </div>
                        @else
                        <div class="list-group">
                            @foreach ($forms as $f)
                            <a href="/admin/forms-assemblies/{{ $f->id }}"
                                class="list-group-item list-group-item-action {{ $form_id == $f->id ? 'active' : '' }}"
                                aria-current="true">{{
                                $f->name }}</a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-8" id="form_ajax"></div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="add_field" tabindex="-1" aria-labelledby="add_field_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_field_label">Add field</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/admin/forms-assemblies/create-form-field" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Label</label>
                        <input type="text" name="label" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control" required>
                            <option value="text">Text</option>
                            <option value="date">Date</option>
                            <option value="email">Email</option>
                            <option value="textarea">Textarea</option>
                            <option value="radio">Rádio</option>
                            <option value="file">File</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </div>
                    <input type="hidden" name="form_id" value="{{ $form_id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://malsup.github.io/jquery.form.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
</script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
    $(() => {
        var form_id = {{ $form_id }};
        getFormAjax(form_id).then(() => {
            $('#sortable').sortable({
                update: (event, ui) => {
                    var cardData = [];
                    $("#sortable .card").each(function(index) {
                        var position = index + 1;
                        var id = $(this).data("id");
                        cardData.push({ id: id, position: position });
                    });
                    $.post({
                        url: '/admin/forms-assemblies/update-positions',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            data: JSON.stringify(cardData)
                        },
                        success: () => {
                            getFormAjax(form_id);
                        },
                        error: (error) => {
                            console.log(error);
                        }
                    });
                }
            });
        });
        $('#add_field form').ajaxForm({
            beforeSubmit: () => {
                $.LoadingOverlay('show');
            },
            success: () => {
                getFormAjax(form_id);
                $.LoadingOverlay('hide');
                $('input').val('');
                $('#add_field').modal('hide');
                $('.modal-backdrop').remove();
            },
            error: () => {
                console.log(error);
            }
        });
    });
    
    getFormAjax = async (form_id) => {
        try {
            return $.get('/admin/forms-assemblies/form-ajax/' + form_id).then((resp) => {
                $('#form_ajax').html(resp);
            });   
        } catch (error) {
            console.log(error);
        }
    }
</script>
@endsection