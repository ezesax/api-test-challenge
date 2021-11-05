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
use File;
use ZipArchive;

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
            $parameters = $request->all();
            $download = $request->input('download');
            unset($parameters['download']);

            $url = $this->buildUrl($parameters);

            $client = new Client();
            $response = $client->get($url);
            $response = $response->getBody();

            if($download == 1){
                return $this->getDownloadContent($response);
            }else{
                return response()->json([
                    'data' => json_decode($response),
                    'message' => 'This is a default endpoint',
                ], 200);
            }
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getDownloadContent($response){
        $file = time() . '_file.json';
                $destinationPath = public_path()."/upload/json/";

                if(!is_dir($destinationPath)){
                    mkdir($destinationPath, 0777, true);
                }

                File::put($destinationPath . $file, $response);

                $zip = new ZipArchive;
                $zipName = 'result.zip';
                if(file_exists(public_path($zipName))){
                    unlink(public_path($zipName));
                }

                if ($zip->open(public_path($zipName), ZipArchive::CREATE) === TRUE){
                    $files = File::files(public_path()."/upload/json/");

                    foreach ($files as $key => $val) {
                        $relativeNameInZipFile = basename($val);
                        $zip->addFile($val, $relativeNameInZipFile);
                    }

                    $zip->close();
                }

                $response = response()->download(public_path($zipName));
                File::delete($destinationPath . $file);

                return $response;
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
