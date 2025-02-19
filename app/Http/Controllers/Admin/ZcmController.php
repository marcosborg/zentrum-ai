<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Iftech;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ZcmController extends Controller
{

    use Iftech;

    public function index()
    {
        abort_if(Gate::denies('zcm_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.zcms.index');
    }

    public function orders(Request $request)
    {
        $access_token =  $this->login()['access_token'];
        $orders = $this->zcmOrders($access_token, $request->start_date, $request->end_date);
        $newArray = [];
        foreach ($orders['orders'] as $order) {
            $newArray[] = [
                'id' => $order['id'],
                'date' => $order['created_at'],
                'client' => $order['client']['name'],
                'salesman' => $order['user']['name'],
                'calltype' => $order['call_type']['name'],
                'status' => $order['status']['name'],
                'car' => ($order['requestlines'] ? $order['requestlines']['car']['brand'] ?? '' : '') . ' ' . ($order['requestlines'] ? $order['requestlines']['car']['brand'] ?? '' : ''),
                'product' => $order['requestlines']['product']['name'] ?? '',
                'request' => $order['requestlines']['obs'] ?? '',
                'declined' => $order['declined'] ? $order['declined']['desc'] : '',
            ];
        }

        return view('admin.zcms.ajax', compact('newArray'));
    }
}
