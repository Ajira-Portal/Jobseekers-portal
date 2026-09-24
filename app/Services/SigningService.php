<?php

namespace App\Services;

use App\Exceptions\SigningException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SigningService
{
    /**
     * @var \OpenSSLAsymmetricKey|resource|null
     */
    private mixed $privateKey = null;

    /**
     * Sign a string using configured algorithm (SHA1withRSA or SHA256withRSA).
     *
     * @param  string  $data  The string to sign
     * @param  int|string|null  $algo  Optional signature algorithm ('sha1' | 'sha256')
     * @return string The Base64 encoded signature
     *
     * @throws SigningException
     */
    public function sign(string $data, int|string|null $algo = null): string
    {
        if (empty($data)) {
            throw new SigningException('Data to sign cannot be empty.');
        }

        Log::info('[SigningService] Starting signature process', ['data_len' => strlen($data), 'algo' => $algo]);

        $privateKey = $this->getPrivateKey();

        if ($algo === null) {
            $configuredAlgo = strtolower((string) Config::get('services.certificate.algo', 'sha1'));
            $algo = match ($configuredAlgo) {
                'sha256' => OPENSSL_ALGO_SHA256,
                default => OPENSSL_ALGO_SHA1,
            };
        } elseif (is_string($algo)) {
            $algo = match (strtolower($algo)) {
                'sha256' => OPENSSL_ALGO_SHA256,
                default => OPENSSL_ALGO_SHA1,
            };
        }

        Log::info('[SigningService] Executing openssl_sign', ['algorithm_id' => $algo]);

        $signature = '';
        $success = openssl_sign($data, $signature, $privateKey, $algo);

        if (! $success) {
            $opensslError = openssl_error_string();
            Log::error('[SigningService] openssl_sign failed', ['openssl_error' => $opensslError]);
            throw new SigningException('Failed to sign data using OpenSSL: ' . ($opensslError ?: 'unknown error'));
        }

        $base64Signature = base64_encode($signature);
        Log::info('[SigningService] Signature created successfully', ['signature_length' => strlen($base64Signature)]);

        return $base64Signature;
    }

    /**
     * Get the private key from the configured PFX certificate.
     *
     * @return \OpenSSLAsymmetricKey|resource
     *
     * @throws SigningException
     */
    private function getPrivateKey(): mixed
    {
        if ($this->privateKey !== null) {
            Log::info('[SigningService] Using cached private key instance');
            return $this->privateKey;
        }

        $config = Config::get('services.certificate');
        Log::info('[SigningService] Loading certificate config', ['config' => $config]);

        if (empty($config['file'])) {
            Log::error('[SigningService] Missing certificate file config');
            throw new SigningException('Signing certificate configuration is missing.');
        }

        $path = $config['path'] ?? 'certificate';
        $certificatePath = storage_path(trim($path, '/').'/'.ltrim($config['file'], '/'));
        Log::info('[SigningService] Resolved certificate path', ['certificatePath' => $certificatePath]);

        if (! File::exists($certificatePath)) {
            Log::error('[SigningService] Certificate file does not exist', ['path' => $certificatePath]);
            throw new SigningException('Signing certificate file does not exist at: ' . $certificatePath);
        }

        $content = File::get($certificatePath);

        if ($content === false) {
            Log::error('[SigningService] Failed to read certificate file contents', ['path' => $certificatePath]);
            throw new SigningException('Unable to read signing certificate file.');
        }

        Log::info('[SigningService] Read certificate file successfully', ['size_bytes' => strlen($content)]);

        // If file is already in PEM format (starts with -----BEGIN)
        if (str_contains($content, '-----BEGIN')) {
            Log::info('[SigningService] Certificate detected as PEM format');
            $privateKey = openssl_pkey_get_private($content, $config['password'] ?? '');
            if ($privateKey === false) {
                $privateKey = openssl_pkey_get_private($content);
            }
            if ($privateKey === false) {
                $opensslError = openssl_error_string();
                Log::error('[SigningService] Failed openssl_pkey_get_private for PEM', ['openssl_error' => $opensslError]);
                throw new SigningException('Invalid PEM private key or incorrect passphrase: ' . ($opensslError ?: 'unknown error'));
            }
            Log::info('[SigningService] Successfully parsed PEM private key');
            $this->privateKey = $privateKey;
            return $this->privateKey;
        }

        // PKCS#12 / PFX format
        Log::info('[SigningService] Certificate detected as PFX format, parsing with openssl_pkcs12_read');
        $certs = [];
        if (! openssl_pkcs12_read($content, $certs, $config['password'] ?? '')) {
            $opensslError = openssl_error_string();
            Log::error('[SigningService] openssl_pkcs12_read failed', ['openssl_error' => $opensslError]);
            throw new SigningException('Unable to parse the signing certificate or incorrect password.');
        }

        if (empty($certs['pkey'])) {
            Log::error('[SigningService] No private key found in PFX array');
            throw new SigningException('Unable to extract private key from the signing certificate.');
        }

        $privateKey = openssl_pkey_get_private($certs['pkey']);

        if ($privateKey === false) {
            $opensslError = openssl_error_string();
            Log::error('[SigningService] Failed openssl_pkey_get_private on extracted PFX key', ['openssl_error' => $opensslError]);
            throw new SigningException('The extracted private key is invalid.');
        }

        Log::info('[SigningService] Successfully extracted private key from PFX');
        $this->privateKey = $privateKey;

        return $this->privateKey;
    }
}
