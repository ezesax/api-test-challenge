<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use GuzzleHttp\Client;

class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getResources(Request $request)
    {
        try{
            $url = $this->buildUrl($request->all());

            $client = new Client();
            $response = $client->get($url);
            $response = $response->getBody();

            return response()->json([
                'data' => json_decode($response),
                'message' => 'This is a default endpoint',
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function buildUrl($parameters){
        $url = env('API_ENDPOINT') . '/' . $parameters['resource'];
        unset($parameters['resource']);

        if(count($parameters) > 0){
            $url .= '?';
            $endItem = end($parameters);

            foreach($parameters as $key => $val){
                $url .= $key . "=" . $val;
                if($val != $endItem){
                    $url .= "&";
                }
            }
        }

        return $url;
    }
}
