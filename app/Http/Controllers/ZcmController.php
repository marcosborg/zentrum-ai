<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Iftech;

class ZcmController extends Controller
{

    use Iftech;

    public function categories()
    {
        $access_token = $this->login()['access_token'];

        $categories = $this->zcmCategories($access_token);

        return $categories;
    }
}
