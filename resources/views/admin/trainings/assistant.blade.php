@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.training.title') }}
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <div class="card">
                    <img src="https://robohash.org/{{ $assistant->name }}">
                    <div class="card-body">
                        <strong>{{ $assistant->name }}</strong>
                        <p><small>{{ $assistant->project->name }}</small></p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success">Sync</button>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Instructions
                    </div>
                    <div class="card-body">
                        <form action="/admin/trainings/instructions/create" id="instructions_create" method="post">
                            @csrf
                            <input type="hidden" name="assistant_id" value="{{ $assistant->id }}">
                            <div class="form-group">
                                <label>Add instruction</label>
                                <textarea class="form-control" name="text"></textarea>
                            </div>
                            <button class="btn btn-success">Add</button>
                        </form>
                    </div>
                    <ul class="list-group list-group-flush" id="instructions_list"></ul>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        Test your assistant
                    </div>
                    <div class="card-body" style="height: 40vh;">
                        <div class="chat-box">
                            <div class="chat">
                                <div class="inner-text">
                                    Em que posso ser útil? Sou o Adriano.
                                </div>
                            </div>
                        </div>
                        <div class="user-box">
                            <div class="user">
                                <div class="inner-text">olá</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group">
                            <label>Mensagem</label>
                            <textarea class="form-control"></textarea>
                        </div>
                        <button class="btn btn-success">Send message</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@section('scripts')
@parent
<script>
    $(() => {
        $('#instructions_create').ajaxForm({
            beforeSubmit: () => {
                $.LoadingOverlay('show');
            },
            success: () => {
                $.LoadingOverlay('hide');
                loadInstructions();
                $('textarea[name="text"]').val('');
                Swal.fire({
                    title: "Success!",
                    text: "New instruction inserted.",
                    icon: "success"
                });
            },
            error: (error) => {
                $.LoadingOverlay('hide');
                var html = '';
                error.responseJSON.errors.text.forEach(element => {
                    html += element + '<br>';
                });
                Swal.fire({
                    title: 'Validation error',
                    icon: 'error',
                    html: html,
                });
            }
        })
        loadInstructions();
    });
    loadInstructions = () => {
        $.LoadingOverlay('show');
        $.get('/admin/trainings/instructions/load/' + {{ $assistant->id }}).then((resp) => {
            $('#instructions_list').html(resp);
            $.LoadingOverlay('hide');
        });
    }
    deleteInstruction = (instruction_id) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                if (result.isConfirmed) {
                    $.LoadingOverlay('show');
                    $.get('/admin/trainings/instructions/delete/' + instruction_id).then(() => {
                        $.LoadingOverlay('hide');
                        loadInstructions();
                        Swal.fire({
                            title: "Deleted!",
                            text: "Your file has been deleted.",
                            icon: "success"
                        });
                    });
            }
        });
    }
</script>
@endsection

@section('styles')
<style>
    .chat {
        border: solid 1px rgb(0 0 255 / 50%);
        display: inline-block;
        padding: 10px;
        background: rgb(0 0 255 / 50%);
        border-radius: 15px;
        color: #ffffff;
    }

    .user {
        border: solid 1px rgb(0 128 0 / 50%);
        display: inline-block;
        padding: 10px;
        background: rgb(0 128 0 / 50%);
        border-radius: 15px;
        color: #ffffff;
    }

    .chat-box {
        margin-bottom: 10px;
        text-align: left;
    }

    .user-box {
        margin-bottom: 10px;
        text-align: right;
    }

    #chat-content {
        overflow-y: scroll;
        height: 40vh;
    }
</style>
@endsection