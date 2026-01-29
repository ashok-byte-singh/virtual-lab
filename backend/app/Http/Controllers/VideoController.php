<?php

namespace App\Http\Controllers;

use App\Models\Experiment;
use App\Services\CloudFrontCookieSigner;

class VideoController extends Controller
{
    public function getVideo(Experiment $experiment)
{
    try {
        $cdnDomain = config('services.cloudfront.domain');
        $cookieDomain = '.asd.mauj.ac.in'; // 🔥 FIXED

        $videoPath = ltrim($experiment->video_url, '/');
        $directory = dirname($videoPath);

        $cookies = CloudFrontCookieSigner::sign($directory, 60);

        return response()->json([
            'playlist' => "https://{$cdnDomain}/{$videoPath}",
            'type'     => 'signed_cookies'
        ])
        ->cookie('CloudFront-Policy', $cookies['CloudFront-Policy'], 60, '/', $cookieDomain, true, true, false, 'None')
        ->cookie('CloudFront-Signature', $cookies['CloudFront-Signature'], 60, '/', $cookieDomain, true, true, false, 'None')
        ->cookie('CloudFront-Key-Pair-Id', $cookies['CloudFront-Key-Pair-Id'], 60, '/', $cookieDomain, true, true, false, 'None');

    } catch (\Throwable $e) {
        \Log::error('Video signing failed', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'message' => 'Video authorization failed'
        ], 500);
    }
 
}

}
