<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Assistant;
use App\Models\Instruction;
use App\Http\Controllers\Traits\OpenAi;
use App\Http\Controllers\Traits\PrestashopApi;

class TrainingController extends Controller
{
    use OpenAi;
    use PrestashopApi;

    public function index()
    {
        abort_if(Gate::denies('training_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assistants = Assistant::all()->load('project');

        return view('admin.trainings.index', compact('assistants'));
    }

    public function assistant($assistant_id)
    {
        abort_if(Gate::denies('training_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assistant = Assistant::find($assistant_id)->load('project');

        return view('admin.trainings.assistant', compact('assistant'));
    }

    public function instructionsCreate(Request $request)
    {

        $request->validate([
            'text' => 'required'
        ], [], [
            'text' => 'Instruction'
        ]);

        $assistant_id = $request->assistant_id;
        $text = $request->text;

        $instructions = Instruction::where('assistant_id', $assistant_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($instructions) {
            $nextPosition = $instructions->position + 1;
        } else {
            $nextPosition = 1;
        }

        $active = 1;

        $instruction = new Instruction;
        $instruction->assistant_id = $assistant_id;
        $instruction->text = $text;
        $instruction->position = $nextPosition;
        $instruction->active = $active;
        $instruction->save();

    }

    public function instructionsLoad($assistant_id)
    {
        $instructions = Instruction::where('assistant_id', $assistant_id)->orderBy('position')->get();

        return view('admin.trainings.instructions-load', compact('instructions'));
    }

    public function instructionDelete($instruction_id)
    {
        Instruction::find($instruction_id)->delete();
    }

    public function syncAssistant(Request $request)
    {

        $assistant_id = $request->assistant_id;
        $assistant = Assistant::find($assistant_id)->load('project.openai');
        $openaiApiKey = $assistant->project->openai->openai_api_key;
        $instructions = $request->instructions;
        $assist_code = $assistant->assist_code;

        return $this->modifyAssistant($openaiApiKey, $assist_code, $instructions);
    }

    public function chatCreateThreadAndRun(Request $request)
    {
        $assistant_id = $request->assistant_id;
        $assistant = Assistant::find($assistant_id)->load('project.openai');
        $openaiApiKey = $assistant->project->openai->openai_api_key;
        $content = $request->message;
        $assist_code = $assistant->assist_code;

        return $this->createThreadAndRun($openaiApiKey, $assist_code, $content);
    }

    public function chatListMessages($assistant_id, $thread_id)
    {
        $assistant = Assistant::find($assistant_id)->load('project.openai');
        $openaiApiKey = $assistant->project->openai->openai_api_key;

        return $this->listMessages($openaiApiKey, $thread_id);

    }

    public function chatCreateMessage(Request $request)
    {

        $assistant_id = $request->assistant_id;
        $assistant = Assistant::find($assistant_id)->load('project.openai');
        $openaiApiKey = $assistant->project->openai->openai_api_key;
        $content = $request->content;
        $thread_id = $request->thread_id;

        return $this->createMessage($openaiApiKey, $thread_id, $content);
    }

    public function chatCreateRun($assistant_id, $thread_id)
    {
        $assistant = Assistant::find($assistant_id)->load('project.openai');
        $openaiApiKey = $assistant->project->openai->openai_api_key;
        $assist_code = $assistant->assist_code;
        return $this->createRun($openaiApiKey, $assist_code, $thread_id);
    }

    public function apiSearch($assistant_id, $search)
    {
        $assistant = Assistant::find($assistant_id)->load('project');
        $project = $assistant->project->name;

        switch ($project) {
            case 'Airbagszentrum':
                $result = $this->zentrumSearch($search);
                break;
            case '':
                break;
            default:
                # code...
                break;
        }

        return $result;
    }

}
