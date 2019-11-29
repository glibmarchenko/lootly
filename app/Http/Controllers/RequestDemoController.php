<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestDemoController extends Controller
{
    public function index(Request $request)
    {
        $ar = array('name' => $request->input("name"), 'email' => $request->input("email"), 'website' => $request->input("website"), 'title' => $request->input("title"));
        $str_post = json_encode($ar);

        $endpoint = 'https://hooks.zapier.com/hooks/catch/3979393/02evuj/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str_post);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $return = array('response'=>$response,'submission'=>$str_post);

        echo json_encode($return);
    }
}
