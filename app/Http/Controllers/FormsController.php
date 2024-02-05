<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;

class FormsController extends Controller
{
    public function index($form_id)
    {
        $form = Form::where('id', $form_id)->with('form_fields')->first();

        return view('website.form', compact('form'));
    }
}
