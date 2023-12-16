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
                        <button class="btn btn-success" onclick="modifyAssistant()"
                            id="modify-assistant-button">Sync</button>
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
                    <div class="card-body" style="height: 40vh;" id="chat-content"></div>
                    <div class="card-footer" id="message-card-footer">
                        <div class="form-group">
                            <label>Mensagem</label>
                            <textarea class="form-control" id="message-textarea"></textarea>
                        </div>
                        <button type="button" class="btn btn-success" onclick="sendMessage()">Send
                            message</button>
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
    const assistant_id = {{ $assistant->id }};
    var thread_id = null;
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
        $.get('/admin/trainings/instructions/load/' + assistant_id).then((resp) => {
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
    modifyAssistant = () => {
        let button = $('#modify-assistant-button');
        button.LoadingOverlay('show');
        var instructions = '';
        $('.instruction-text').each(function() {
            instructions += $(this).text() + ' ';
        });
        let data = {
            instructions: instructions,
            assistant_id: assistant_id
        }
        $.post({
            url: '/admin/trainings/instructions/sync-assistant',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            success: () => {
                button.LoadingOverlay('hide');
                Swal.fire('Updated');
            },
            error: (error) => {
                button.LoadingOverlay('hide');
                Swal.fire('Sync error');
            }
        });
    }
    sendMessage = () => {
        let message = $('#message-textarea').val();
        if(message.length > 0) {
            $('#message-textarea').val('');
            addMessage('user', message);
            //OVERLAY
            let message_card_footer = $('#message-card-footer');
            message_card_footer.LoadingOverlay('show');
            let data = {
                assistant_id: assistant_id,
                message: message
            }
            if(!thread_id){
                //Create thread and run
                $.post({
                    url: '/admin/trainings/chat/create-thread-and-run',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: (resp) => {
                        thread_id = resp.thread_id;
                        //List messages
                        let lm = setInterval(() => {
                            listMessages(thread_id).then((resp) => {
                            message = resp.data[0].content[0].text.value;
                            if (resp.data[0].role != 'user' && message.length > 0) {
                                clearInterval(lm);
                                addMessage('assistant', message);
                                message_card_footer.LoadingOverlay('hide');
                            }
                        });
                        }, 3000);
                    }
                });
            } else {
                //Create message
                let data = {
                    content: message,
                    thread_id: thread_id,
                    assistant_id: assistant_id
                }
                $.post({
                    url: '/admin/trainings/chat/create-message',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: () => {
                        $.get('/admin/trainings/chat/create-run/' + assistant_id + '/' + thread_id).then(() => {
                            //List messages
                            let lm = setInterval(() => {
                                listMessages(thread_id).then((resp) => {
                                    message = resp.data[0].content[0].text.value;
                                    if (resp.data[0].role != 'user' && message.length > 0) {
                                        clearInterval(lm);
                                        addMessage('assistant', message);
                                        $search = extractText(message);
                                        if ($search){
                                            message_card_footer.LoadingOverlay('hide');
                                            //API
                                            getProduct($search).then((resp) => {
                                                if(resp.length > 0){
                                                    //ASSEMBLE LINKS
                                                } else {
                                                    //NO RESULTS
                                                }
                                            });
                                        } else {
                                            message_card_footer.LoadingOverlay('hide');
                                        }
                                    }
                                });
                            }, 3000);
                        });
                    }
                });
            }
        }
    }
    listMessages = async () => {
        try {
            const messages = await $.get('/admin/trainings/chat/list-messages/' + assistant_id + '/' + thread_id);
            return messages
        } catch (error) {
            return error;
        }
    }

    addMessage = (role, text) => {
        let html = '';
        switch (role) {
            case 'user':
                html += '<div class="user-box">';
                html += '<div class="user">';
                html += '<div class="inner-text">' + text + '</div>';
                html += '</div>';
                html += '</div>';
                break;
            default:
                html += '<div class="chat-box">';
                html += '<div class="chat">';
                html += '<div class="inner-text">' + text + '</div>';
                html += '</div>';
                html += '</div>';
            break;
        }
        let chatContent = $('#chat-content');
        chatContent.append(html);
        chatContent.scrollTop(chatContent[0].scrollHeight);
    }

    function extractText(str) {
        const regex = /Estou a procurar: "([^"]*)"/;
        const matches = str.match(regex);

        if (matches && matches[1]) {
            return matches[1];
        } else {
            return null;
        }
    }


    getProduct = async (search) => {
        try {
            return await $.get('/admin/trainings/api/search/' + assistant_id + '/' + search);
        } catch (error) {
            return error;
        }
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