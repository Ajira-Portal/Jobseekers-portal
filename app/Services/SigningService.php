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

        if (empty($config['file'])) {
            throw new SigningException('Signing certificate configuration is missing.');
        }

        $file = ltrim((string) ($config['file'] ?? ''), '/\\');
        $path = trim((string) ($config['path'] ?? 'certificate'), '/\\');

        $candidates = array_unique([
            base_path($path . '/' . $file),
            base_path('storage/certificate/' . $file),
            base_path('certificate/' . $file),
            storage_path($file),
            storage_path($path . '/' . $file),
            storage_path('certificate/' . $file),
            '/var/www/html/storage/certificate/' . $file,
            '/var/www/html/certificate/' . $file,
        ]);

        $certificatePath = null;
        $checkedPaths = [];
        foreach ($candidates as $candidate) {
            $checkedPaths[] = $candidate;
            if (file_exists($candidate) || File::exists($candidate)) {
                $certificatePath = $candidate;
                break;
            }
        }

        if (!$certificatePath) {
            $tried = implode(' | ', $checkedPaths);
            Log::error("Certificate file '{$file}' not found. Checked paths: {$tried}");
            throw new SigningException("Signing certificate file '{$file}' does not exist. Checked: {$tried}");
        }

        $content = File::get($certificatePath);

        if ($content === false) {
            throw new SigningException('Unable to read signing certificate file.');
        }

        // If file is already in PEM format (starts with -----BEGIN)
        if (str_contains($content, '-----BEGIN')) {
            $privateKey = openssl_pkey_get_private($content, $config['password'] ?? '');
            if ($privateKey === false) {
                $privateKey = openssl_pkey_get_private($content);
            }
            if ($privateKey === false) {
                throw new SigningException('Invalid PEM private key or incorrect passphrase.');
            }
            $this->privateKey = $privateKey;
            return $this->privateKey;
        }

        // PKCS#12 / PFX format
        $certs = [];
        if (! openssl_pkcs12_read($content, $certs, $config['password'] ?? '')) {
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
