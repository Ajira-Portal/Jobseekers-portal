<?php

namespace App\Services;

use App\Exceptions\SigningException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SigningService
{
    private mixed $privateKey = null;

    public function sign(string $data): string
    {
        Log::info('[SigningService] sign() called with data payload.', ['data_length' => strlen($data)]);
        
        if (empty($data)) {
            Log::error('[SigningService] Data to sign is empty.');
            throw new SigningException('Data to sign cannot be empty.');
        }

        $privateKey = $this->getPrivateKey();
        $signature = '';

        Log::info('[SigningService] Attempting to sign data using OPENSSL_ALGO_SHA1.');
        $success = openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA1);

        if (! $success) {
            $error = openssl_error_string();
            Log::error('[SigningService] Failed to sign data.', ['openssl_error' => $error]);
            throw new SigningException('Failed to sign data using SHA1withRSA.');
        }

        Log::info('[SigningService] Data signed successfully.');
        return base64_encode($signature);
    }

    private function getPrivateKey(): mixed
    {
        if ($this->privateKey !== null) {
            return $this->privateKey;
        }

        Log::info('[SigningService] Loading configuration from services.certificate.');
        $config = Config::get('services.certificate');

        if (empty($config['file']) || empty($config['password']) || empty($config['alias'])) {
            Log::error('[SigningService] Configuration missing (file, password, or alias).', ['config' => $config]);
            throw new SigningException('Signing certificate configuration is missing.');
        }

        $path = $config['path'] ?? 'certificate';
        $certificatePath = storage_path(trim($path, '/').'/'.ltrim($config['file'], '/'));
        Log::info('[SigningService] Certificate path resolved.', ['certificatePath' => $certificatePath]);

        if (! File::exists($certificatePath)) {
            Log::error('[SigningService] Certificate file does not exist.', ['certificatePath' => $certificatePath]);
            throw new SigningException('Signing certificate file does not exist: ' . $certificatePath);
        }

        Log::info('[SigningService] Reading certificate file contents.');
        $pfxContent = File::get($certificatePath);

        if ($pfxContent === false || empty($pfxContent)) {
            Log::error('[SigningService] Certificate file is empty or could not be read.', ['certificatePath' => $certificatePath]);
            throw new SigningException('Unable to read signing certificate file.');
        }

        $certs = [];
        Log::info('[SigningService] Attempting to parse PKCS#12 (.pfx) certificate.', ['password_length' => strlen($config['password'])]);
        
        if (! openssl_pkcs12_read($pfxContent, $certs, $config['password'])) {
            $error = openssl_error_string();
            Log::error('[SigningService] openssl_pkcs12_read failed.', ['openssl_error' => $error]);
            throw new SigningException('Unable to parse the signing certificate or incorrect password.');
        }

        if (empty($certs['pkey'])) {
            Log::error('[SigningService] Private key not found in the parsed PFX array.');
            throw new SigningException('Unable to extract private key from the signing certificate.');
        }

        Log::info('[SigningService] Extracting private key using openssl_pkey_get_private.');
        $privateKey = openssl_pkey_get_private($certs['pkey']);

        if ($privateKey === false) {
            $error = openssl_error_string();
            Log::error('[SigningService] openssl_pkey_get_private failed.', ['openssl_error' => $error]);
            throw new SigningException('The extracted private key is invalid.');
        }

        Log::info('[SigningService] Private key successfully loaded and validated.');
        $this->privateKey = $privateKey;

        return $this->privateKey;
    }
}
