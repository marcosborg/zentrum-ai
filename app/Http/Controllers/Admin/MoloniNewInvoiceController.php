<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Moloni;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Session;

class MoloniNewInvoiceController extends Controller
{
    use Moloni;

    public function index()
    {
        abort_if(Gate::denies('moloni_new_invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Se já temos token na sessão, não autenticar de novo (opcional)
        if (!Session::has('moloni_access_token')) {
            $auth = $this->authenticateMoloni();

            if ($auth && isset($auth['access_token'])) {
                // Guardar tokens na sessão
                Session::put('moloni_access_token', $auth['access_token']);
                Session::put('moloni_refresh_token', $auth['refresh_token']);
                Session::put('moloni_token_expires_at', now()->addSeconds($auth['expires_in']));
            }
        }

        return view('admin.moloniNewInvoices.index');
    }

    // Endpoint para renovar o token via AJAX
    public function refreshTokenAjax()
    {
        if (!Session::has('moloni_refresh_token')) {
            return response()->json(['error' => 'No refresh token found'], 400);
        }

        $refreshToken = Session::get('moloni_refresh_token');
        $newToken = $this->refreshMoloniToken($refreshToken);

        if ($newToken && isset($newToken['access_token'])) {
            // Atualiza a sessão
            Session::put('moloni_access_token', $newToken['access_token']);
            Session::put('moloni_refresh_token', $newToken['refresh_token']);
            Session::put('moloni_token_expires_at', now()->addSeconds($newToken['expires_in']));

            return response()->json(['success' => true, 'access_token' => $newToken['access_token']]);
        }

        return response()->json(['error' => 'Failed to refresh token'], 500);
    }
}
