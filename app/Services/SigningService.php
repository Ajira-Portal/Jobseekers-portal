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

        $signature = '';
        $success = openssl_sign($data, $signature, $privateKey, $algo);

        if (! $success) {
            throw new SigningException('Failed to sign data using OpenSSL.');
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

        if (empty($config['file']) || empty($config['password']) || empty($config['alias'])) {
            throw new SigningException('Signing certificate configuration is missing.');
        }

        $path = $config['path'] ?? 'certificate';
        $certificatePath = storage_path(trim($path, '/').'/'.ltrim($config['file'], '/'));

        if (! File::exists($certificatePath)) {
            $certificatePath = base_path(trim($path, '/').'/'.ltrim($config['file'], '/'));
        }

        if (! File::exists($certificatePath)) {
            throw new SigningException('Signing certificate file does not exist.');
        }

        $pfxContent = File::get($certificatePath);

        if ($pfxContent === false) {
            throw new SigningException('Unable to read signing certificate file.');
        }

        $certs = [];
        if (! openssl_pkcs12_read($pfxContent, $certs, $config['password'])) {
            throw new SigningException('Unable to parse the signing certificate or incorrect password.');
        }

        if (empty($certs['pkey'])) {
            throw new SigningException('Unable to extract private key from the signing certificate.');
        }

        $privateKey = openssl_pkey_get_private($certs['pkey']);

        if ($privateKey === false) {
            throw new SigningException('The extracted private key is invalid.');
        }

        $this->privateKey = $privateKey;

        return $this->privateKey;
    }
}
