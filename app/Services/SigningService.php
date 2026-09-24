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
     * Sign a string using SHA1withRSA.
     *
     * @param  string  $data  The string to sign
     * @return string The Base64 encoded signature
     *
     * @throws SigningException
     */
    public function sign(string $data): string
    {
        if (empty($data)) {
            throw new SigningException('Data to sign cannot be empty.');
        }

        $privateKey = $this->getPrivateKey();

        $signature = '';
        $success = openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA1);

        if (! $success) {
            throw new SigningException('Failed to sign data using SHA1withRSA.');
        }

        return base64_encode($signature);
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
            return $this->privateKey;
        }

        $config = Config::get('services.certificate');

        Log::info('[SigningService] Loading private key from certificate config', ['file' => $config['file'] ?? null]);

        if (empty($config['file'])) {
            Log::error('[SigningService] Certificate file is not configured.');
            throw new SigningException('Signing certificate configuration is missing.');
        }

        $path = $config['path'] ?? 'certificate';
        $certificatePath = storage_path(trim($path, '/').'/'.ltrim($config['file'], '/'));
        Log::info('[SigningService] Resolved certificate path', ['certificatePath' => $certificatePath]);

        if (! File::exists($certificatePath)) {
            Log::error('[SigningService] Certificate file does not exist at: ' . $certificatePath);
            throw new SigningException('Signing certificate file does not exist.');
        }

        $content = File::get($certificatePath);

        if ($content === false) {
            Log::error('[SigningService] Unable to read certificate file contents.');
            throw new SigningException('Unable to read signing certificate file.');
        }

        // PEM Format support (e.g., zanidtestprivate.pem)
        if (str_contains($content, '-----BEGIN')) {
            Log::info('[SigningService] Reading certificate in PEM format');
            $passphrase = $config['password'] ?? '';
            $privateKey = openssl_pkey_get_private($content, $passphrase);
            if ($privateKey === false) {
                // Try without passphrase if failed
                $privateKey = openssl_pkey_get_private($content);
            }

            if ($privateKey === false) {
                $err = openssl_error_string();
                Log::error('[SigningService] Invalid PEM private key or passphrase', ['openssl_error' => $err]);
                throw new SigningException('Invalid PEM private key or incorrect passphrase.');
            }

            Log::info('[SigningService] Successfully loaded PEM private key');
            $this->privateKey = $privateKey;
            return $this->privateKey;
        }

        // PKCS#12 / PFX Format support
        Log::info('[SigningService] Reading certificate in PFX format');
        $certs = [];
        if (! openssl_pkcs12_read($content, $certs, $config['password'] ?? '')) {
            $err = openssl_error_string();
            Log::error('[SigningService] openssl_pkcs12_read failed', ['openssl_error' => $err]);
            throw new SigningException('Unable to parse the signing certificate or incorrect password.');
        }

        if (empty($certs['pkey'])) {
            Log::error('[SigningService] No private key found in PFX array');
            throw new SigningException('Unable to extract private key from the signing certificate.');
        }

        $privateKey = openssl_pkey_get_private($certs['pkey']);

        if ($privateKey === false) {
            $err = openssl_error_string();
            Log::error('[SigningService] Failed openssl_pkey_get_private on extracted PFX key', ['openssl_error' => $err]);
            throw new SigningException('The extracted private key is invalid.');
        }

        Log::info('[SigningService] Successfully loaded PFX private key');
        $this->privateKey = $privateKey;

        return $this->privateKey;
    }
}
