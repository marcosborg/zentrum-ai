<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormData;
use App\Http\Controllers\Traits\Iftech;

class FormsController extends Controller
{

    use Iftech;

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

        $data = json_encode($request->data);

        //SEND BY API

        //LOGIN

        $access_token = $this->login()['access_token'];

        $send_form = $this->sendForm($access_token, $data);

        return $send_form;

    }
}
