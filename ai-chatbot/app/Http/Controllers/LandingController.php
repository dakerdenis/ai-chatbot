<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing');
    }

    public function widget(Request $request)
    {
        // Пытаемся отдать из public/, иначе из resources/
        $path = public_path('widget.js');
        if (!is_file($path)) {
            $alt = resource_path('js/widget.js');
            if (!is_file($alt)) {
                abort(404, 'Widget not found');
            }
            $path = $alt;
        }

        $mtime = filemtime($path);
        $etag  = sha1($path . '|' . $mtime . '|' . filesize($path));
        $lastModified = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';

        // 304 при совпадении ETag / If-Modified-Since
        $reqEtag = $request->headers->get('If-None-Match');
        $reqSince = strtotime($request->headers->get('If-Modified-Since') ?? '') ?: 0;
        if ($reqEtag === $etag || $reqSince >= $mtime) {
            return response('', 304)->withHeaders([
                'Content-Type'              => 'application/javascript; charset=UTF-8',
                'Cache-Control'             => 'public, max-age=86400, s-maxage=86400',
                'ETag'                      => $etag,
                'Last-Modified'             => $lastModified,
                'X-Content-Type-Options'    => 'nosniff',
            ]);
        }

        return response()->file($path, [
            'Content-Type'              => 'application/javascript; charset=UTF-8',
            'Cache-Control'             => 'public, max-age=86400, s-maxage=86400',
            'ETag'                      => $etag,
            'Last-Modified'             => $lastModified,
            'X-Content-Type-Options'    => 'nosniff',
        ]);
    }
}
