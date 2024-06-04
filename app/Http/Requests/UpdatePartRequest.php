<?php

namespace App\Http\Requests;

use App\Models\Part;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePartRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('part_edit');
    }

    public function rules()
    {
        return [
            'photo' => [
                'required',
            ],
            'exist' => [
                'required',
            ],
        ];
    }
}
