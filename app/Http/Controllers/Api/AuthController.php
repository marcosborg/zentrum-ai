<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\FormData;
use App\Http\Controllers\Traits\PrestashopApi;

class AuthController extends Controller
{

    use PrestashopApi;

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

        //$image = 'https://ai.airbagszentrum.com/images/aR5nNSA8Ma.jpg';

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
        )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        $ocrText = json_decode($response)->ParsedResults[0]->ParsedText;

        // Separar as linhas
        $lines = explode("\n", $ocrText);

        // Remover espaços extras de cada linha
        $cleanedLines = array_map('trim', $lines);

        // Opcional: Remover linhas vazias, se houver
        $cleanedLines = array_filter($cleanedLines, function ($line) {
            return !empty($line);
        });

        // Resetar as chaves do array (opcional, se precisar de um array indexado)
        $cleanedLines = array_values($cleanedLines);

        // Remover todos os espaços de cada item do array
        $cleanedArray = array_map(function ($item) {
            return str_replace(' ', '', $item);
        }, $cleanedLines);

        // Filtrar o array usando a função isValidReference
        $filteredArray = array_filter($cleanedArray, [$this, 'isValidReference']);

        // Reindexar o array
        $filteredArray = array_values($filteredArray);

        // Exibir o resultado
        return response()->json($filteredArray);
    }

    // Função para determinar se um item é uma referência válida
    public function isValidReference($item)
    {
        // Verificar se o item tem mais de 5 caracteres e menos de 14 caracteres
        $length = strlen($item);
        if ($length <= 5 || $length >= 14) {
            return false;
        }

        // Verificar se o item contém apenas letras e números
        if (preg_match('/[^A-Za-z0-9]/', $item)) {
            return false;
        }

        return true;
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

    public function searchStock(Request $request)
    {

        $product = null;

        foreach ($request->codes as $code) {
            $result = $this->zentrumSearch('https://techniczentrum.com', $code);
            if ($result) {
                $product = $result;
            }
        }
        return $product;
    }

    public function updateState($form_data_id)
    {
        $form_data = FormData::find($form_data_id);
        $form_data->done = 1;
        $form_data->save();
    }

    public function prestashopCategories()
    {

        return $this->categories();
    }

    public function prestashopManufacturers()
    {
        return $this->manufacturers();
    }

    public function prestashopCategory($category_id)
    {
        return $this->category($category_id);
    }

    public function prestashopManufacturer($manufacturer_id)
    {
        return $this->manufacturer($manufacturer_id);
    }

    public function createProduct(Request $request)
    {
        return $this->newProduct($request);

    }

    public function uploadImage(Request $request)
    {
        return $this->savePhoto(($request));
    }
}
