<?php

namespace App\Services;

use App\Exceptions\SigningException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

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
     * @return string  The Base64 encoded signature
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
     * Get the private key from the configured PEM or PFX certificate.
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

        if (empty($config['file'])) {
            throw new SigningException('Signing certificate file configuration is missing.');
        }

        $path = $config['path'] ?? 'certificate';
        
        if (str_starts_with($path, '/') || str_starts_with($path, '\\') || preg_match('/^[a-zA-Z]:(\\\|\/)/', $path)) {
            $certificatePath = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . ltrim($config['file'], '/\\');
        } else {
            $certificatePath = storage_path(trim($path, '/\\') . DIRECTORY_SEPARATOR . ltrim($config['file'], '/\\'));
        }

        if (! File::exists($certificatePath)) {
            throw new SigningException('Signing certificate file does not exist at: ' . $certificatePath);
        }

        $content = File::get($certificatePath);

        if ($content === false || empty($content)) {
            throw new SigningException('Unable to read signing certificate file.');
        }

        $password = $config['password'] ?? '';
        $privateKey = false;

        if (str_contains($content, '-----BEGIN')) {
            $privateKey = openssl_pkey_get_private($content, $password);
            if ($privateKey === false) {
                $privateKey = openssl_pkey_get_private($content);
            }
        } else {
            $certs = [];
            if (! openssl_pkcs12_read($content, $certs, $password)) {
                throw new SigningException('Unable to parse the signing certificate or incorrect password.');
            }

            if (empty($certs['pkey'])) {
                throw new SigningException('Unable to extract private key from the signing certificate.');
            }

            $privateKey = openssl_pkey_get_private($certs['pkey']);
        }

        if ($privateKey === false) {
            throw new SigningException('The private key is invalid or requires a different password.');
        }

        $this->privateKey = $privateKey;

        return $this->privateKey;
    }
}
