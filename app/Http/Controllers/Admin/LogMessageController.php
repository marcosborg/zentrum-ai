<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyLogMessageRequest;
use App\Http\Requests\StoreLogMessageRequest;
use App\Http\Requests\UpdateLogMessageRequest;
use App\Models\Log;
use App\Models\LogMessage;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogMessageController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('log_message_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $logMessages = LogMessage::with(['log'])->get();

        return view('admin.logMessages.index', compact('logMessages'));
    }

    public function create()
    {
        abort_if(Gate::denies('log_message_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $logs = Log::pluck('project', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.logMessages.create', compact('logs'));
    }

    public function store(StoreLogMessageRequest $request)
    {
        $logMessage = LogMessage::create($request->all());

        return redirect()->route('admin.log-messages.index');
    }

    public function edit(LogMessage $logMessage)
    {
        abort_if(Gate::denies('log_message_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $logs = Log::pluck('project', 'id')->prepend(trans('global.pleaseSelect'), '');

        $logMessage->load('log');

        return view('admin.logMessages.edit', compact('logMessage', 'logs'));
    }

    public function update(UpdateLogMessageRequest $request, LogMessage $logMessage)
    {
        $logMessage->update($request->all());

        return redirect()->route('admin.log-messages.index');
    }

    public function show(LogMessage $logMessage)
    {
        abort_if(Gate::denies('log_message_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $logMessage->load('log');

        return view('admin.logMessages.show', compact('logMessage'));
    }

    public function destroy(LogMessage $logMessage)
    {
        abort_if(Gate::denies('log_message_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $logMessage->delete();

        return back();
    }

    public function massDestroy(MassDestroyLogMessageRequest $request)
    {
        $logMessages = LogMessage::find(request('ids'));

        foreach ($logMessages as $logMessage) {
            $logMessage->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
