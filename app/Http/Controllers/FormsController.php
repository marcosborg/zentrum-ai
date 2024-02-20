<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormData;

class FormsController extends Controller
{

    public function index($form_id)
    {
        $form = Form::where('id', $form_id)->with('form_fields')->first();

        return view('website.form', compact('form'));
    }

    public function formSend(Request $request)
    {
        $form_data = new FormData;
        $form_data->form_id = $request->form_id;
        $form_data->data = json_encode($request->data);
        $form_data->save();

        //SEND BY API

        
    }
}
