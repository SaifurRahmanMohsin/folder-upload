<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DropboxController extends Controller
{
    public function store(Request $request)
    {
        $file = $request->file('file');
        $path = $request->input('path');

        $fp = fopen($file->getRealPath(), 'rb');
        $size = $file->getSize();

        $dropbox_key = env('DROPBOX_KEY');

        $cheaders = array('Authorization: Bearer ' . $dropbox_key,
                  'Content-Type: application/octet-stream',
                  'Dropbox-API-Arg: {"path":"/MyApp/' . $path . '", "mode":"add"}');

        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        echo $response;
        curl_close($ch);
        fclose($fp);
    }
}
