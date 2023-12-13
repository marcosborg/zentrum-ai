<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Assistant;
use App\Models\Instruction;

class TrainingController extends Controller
{
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

}
