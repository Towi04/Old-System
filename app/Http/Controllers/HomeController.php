<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function ver_archivo($modulo, $id, $archivo = null)
    {
        $storage_path = storage_path();
        $public_path = public_path();

        if ($id === 'no') { //Si no se manda id, se busca en la carpeta temporal el archivo

            $url = "{$storage_path}/app/temp/{$archivo}";

            # verificamos si el archivo existe y lo retornamos
            if (!Storage::exists("temp/{$archivo}")) {
                # Cambiar por una imagen generica
                return response()->download("{$public_path}/no_image/{$modulo}.png");
            }

            return response()->download($url);
        } else {
            $url = "{$storage_path}/app/{$modulo}/{$id}/{$archivo}";

            # verificamos si el archivo existe y lo retornamos
            if (Storage::exists("{$modulo}/{$id}/{$archivo}") && $archivo != '') {

                if (Str::endsWith($url, '.pdf')) {
                    return Response::make(
                        file_get_contents($url),
                        200,
                        [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline;',
                        ]
                    );
                }

                if (Str::endsWith($url, '.txt')) {
                    return Response::make(
                        file_get_contents($url),
                        200,
                        [
                            'Content-Type' => 'text/html',
                            'Content-Disposition' => 'inline;',
                        ]
                    );
                }

                if (Str::endsWith($url, '.jpg') || Str::endsWith($url, '.png') || Str::endsWith($url, '.gif') || Str::endsWith($url, '.JPG') || Str::endsWith($url, '.PNG') || Str::endsWith($url, '.jpeg') ) {
                    return Response::make(
                        file_get_contents($url),
                        200,
                        [
                            'Content-Type' => 'image/jpeg',
                            'Content-Disposition' => 'inline;',
                        ]
                    );
                }


                return response()->download($url);

            } else {
                return response()->download("{$public_path}/no_image/{$modulo}.png");
            }
        }
    }
}
