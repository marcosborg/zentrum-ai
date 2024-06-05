<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\FormData;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $credentials = request(['email', 'password']);
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'password' => [
                        'Invalid credentials'
                    ],
                ]
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $authToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'access_token' => $authToken,
        ]);
    }

    public function sendPhoto(Request $request)
    {

        $base64String = $request->imageUrl;

        if (strpos($base64String, 'data:image/') === 0) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
        }

        $imageData = base64_decode($base64String);

        $fileName = Str::random(10) . '.jpg';

        $filePath = public_path('images/' . $fileName);

        File::put($filePath, $imageData);

        $image = 'https://ai.airbagszentrum.com/images/' . $fileName;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.ocr.space/parse/imageurl?apikey=' . env('OCR_API') . '&url=' . $image,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response)['ParsedResults'];
        
    }

    public function getUser(Request $request)
    {
        $user = auth()->user();
        return $user;
    }

    public function updateUser(Request $request)
    {
        if ($request->password == '') {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
            ], [], [
                'name' => 'Nome',
                'email' => 'Email'
            ]);
        } else {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|max:255',
                'password' => 'required|min:6',
                'password_confirm' => 'same:password'
            ], [], [
                'name' => 'Nome',
                'email' => 'Email',
                'password' => 'Password',
                'password_confirm' => 'Confirmação da password'
            ]);
        }

        $user = User::find(auth()->user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();
        return $user;
    }

    public function formDatas()
    {
        $form_datas = FormData::where([
            'form_id' => 3,
            'done' => false,
        ])->orderBy('created_at', 'desc')->get();
        $convertedFormDatas = $form_datas->map(function ($form_data) {
            $form_data->data = json_decode($form_data->data, true);
            return $form_data;
        });

        return $convertedFormDatas;
    }

    public function formData($form_data_id)
    {
        $form_data = FormData::find($form_data_id);
        $form_data->data = json_decode($form_data->data, true);
        return $form_data;
    }
}
