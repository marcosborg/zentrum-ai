<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{

    public function assistant($assistant_id)
    {

        $assistant = Assistant::find($assistant_id)->load('project');

        return view('website.home', compact('assistant'));
    }

}
