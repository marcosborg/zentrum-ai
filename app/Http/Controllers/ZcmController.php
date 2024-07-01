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

    public function subCategories($phase_id)
    {

        $access_token = $this->login()['access_token'];

        $subCategories = $this->zcmSubCategories($access_token, $phase_id);

        return $subCategories;
    }

    public function zcmUpdateState(Request $request)
    {
        $access_token = $this->login()['access_token'];
        
        return $this->followup($access_token, $request->id, $request->phase_id, $request->status_id, $request->obs);
    }
}
