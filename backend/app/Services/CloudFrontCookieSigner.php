<?php

namespace App\Services;

class CloudFrontCookieSigner
{
    public static function sign(string $resourcePath, int $minutes = 60): array
    {
        $expires = time() + ($minutes * 60);

        $policy = json_encode([
            'Statement' => [[
                'Resource' => "https://" . config('services.cloudfront.domain') . "/{$resourcePath}/*",
                'Condition' => [
                    'DateLessThan' => [
                        'AWS:EpochTime' => $expires
                    ]
                ]
            ]]
        ], JSON_UNESCAPED_SLASHES);

        $privateKeyPath = config('services.cloudfront.private_key');
        $privateKey = file_get_contents($privateKeyPath);

        if (!$privateKey) {
            throw new \RuntimeException('CloudFront private key not readable');
        }

        $signature = '';
        openssl_sign($policy, $signature, $privateKey, OPENSSL_ALGO_SHA1);

        return [
            'CloudFront-Policy'      => self::urlSafeBase64($policy),
            'CloudFront-Signature'   => self::urlSafeBase64($signature),
            'CloudFront-Key-Pair-Id' => config('services.cloudfront.key_pair_id'),
        ];
    }

    private static function urlSafeBase64(string $data): string
    {
        return str_replace(
            ['+', '=', '/'],
            ['-', '_', '~'],
            base64_encode($data)
        );
    }
}
