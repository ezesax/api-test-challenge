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

    public function getCharacters()
    {
        try{
            $baseUrl = env('API_ENDPOINT');
            $url = $baseUrl . '/character';

            $client = new Client();
            $request = $client->get($url);
            $response = $request->getBody();

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
}
