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
        

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                    "model": "gpt-4-turbo",
                    "messages": [
                    {
                        "role": "user",
                        "content": [
                        {
                            "type": "text",
                            "text": "Quero que tentes identificar a peça automóvel e que devolvas num json o nome da peça em portugues, bem como todas as referencias que encontrares nas etiquetas. Não quero uma descrição. Apenas name e references num array."
                        },
                        {
                            "type": "image_url",
                            "image_url": {
                            "url": "' . $image . '"
                            }
                        }
                        ]
                    }
                    ],
                    "max_tokens": 300
                }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer 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',
                    'Cookie: __cf_bm=mfjslVS8TdkRobV1VnhRAWjyJW8YwpNfSjg3mWEoq2g-1717500710-1.0.1.1-l5.TgeAi6Kde5os1.ebnIwm9jqTGCP5i42yQKCI6Ou_SneBF1Jkz7aMXtri7Cb9Sk85RriqRe6Vja7ItaZQVWA; _cfuvid=dbSe_.tGKExExdaG63Bek8FgTdciY6ksZLchLe5qc.g-1717500710977-0.0.1.1-604800000'
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
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
