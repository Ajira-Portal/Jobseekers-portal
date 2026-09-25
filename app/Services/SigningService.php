<?php

namespace App\Services;

use App\Exceptions\SigningException;
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
     * @param string $data
     * @return string Base64 encoded signature
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

        $success = openssl_sign(
            $data,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        if (! $success) {
            $error = openssl_error_string();

            Log::error('[SigningService] Failed to sign data', [
                'openssl_error' => $error,
            ]);

            throw new SigningException(
                'Failed to sign data using SHA1withRSA.'
            );
        }

        return base64_encode($signature);
    }

    /**
     * Load the private key from:
     *
     * storage/certificate/zanidtestprivate.pem
     *
     * Configuration:
     *
     * SIGNING_CERTIFICATE_PATH=certificate
     * SIGNING_CERTIFICATE_FILE=zanidtestprivate.pem
     *
     * @return \OpenSSLAsymmetricKey|resource
     *
     * @throws SigningException
     */
    private function getPrivateKey(): mixed
    {
        // Return cached key if already loaded
        if ($this->privateKey !== null) {
            return $this->privateKey;
        }

        /*
         * Resolve certificate directory relative to Laravel storage.
         *
         * storage_path() points to:
         *
         * /opt/jobseekers/storage
         *
         * Therefore:
         *
         * storage_path('certificate')
         *
         * becomes:
         *
         * /opt/jobseekers/storage/certificate
         */
        $certificateDirectory = storage_path(
            trim(env('SIGNING_CERTIFICATE_PATH', 'certificate'), '/\\')
        );

        $certificateFile = env(
            'SIGNING_CERTIFICATE_FILE',
            'zanidtestprivate.pem'
        );

        $certificatePath = $certificateDirectory
            . DIRECTORY_SEPARATOR
            . $certificateFile;

        Log::info('[SigningService] Loading signing certificate', [
            'certificate_path' => $certificatePath,
        ]);

        /*
         * Check certificate file exists.
         */
        if (! File::exists($certificatePath)) {
            Log::error(
                '[SigningService] Certificate file does not exist',
                [
                    'certificate_path' => $certificatePath,
                    'certificate_directory' => $certificateDirectory,
                    'certificate_file' => $certificateFile,
                ]
            );

            throw new SigningException(
                'Signing certificate file does not exist: '
                . $certificatePath
            );
        }

        /*
         * Read certificate contents.
         */
        $content = File::get($certificatePath);

        if ($content === false || empty($content)) {
            Log::error(
                '[SigningService] Unable to read certificate file',
                [
                    'certificate_path' => $certificatePath,
                ]
            );

            throw new SigningException(
                'Unable to read signing certificate file.'
            );
        }

        /*
         * ==========================================================
         * PEM PRIVATE KEY
         * ==========================================================
         */
        if (
            str_contains($content, '-----BEGIN PRIVATE KEY-----') ||
            str_contains($content, '-----BEGIN RSA PRIVATE KEY-----') ||
            str_contains($content, '-----BEGIN ENCRYPTED PRIVATE KEY-----')
        ) {
            Log::info(
                '[SigningService] Reading certificate as PEM private key'
            );

            $passphrase = env(
                'SIGNING_CERTIFICATE_PASSWORD',
                ''
            );

            $privateKey = openssl_pkey_get_private(
                $content,
                $passphrase
            );

            if ($privateKey === false) {
                $error = openssl_error_string();

                Log::error(
                    '[SigningService] Unable to load PEM private key',
                    [
                        'openssl_error' => $error,
                    ]
                );

                throw new SigningException(
                    'Invalid PEM private key or incorrect passphrase.'
                );
            }

            Log::info(
                '[SigningService] PEM private key loaded successfully'
            );

            $this->privateKey = $privateKey;

            return $this->privateKey;
        }

        /*
         * ==========================================================
         * PKCS#12 / PFX
         * ==========================================================
         */
        Log::info(
            '[SigningService] Reading certificate as PKCS#12/PFX'
        );

        $certs = [];

        $password = env(
            'SIGNING_CERTIFICATE_PASSWORD',
            ''
        );

        if (! openssl_pkcs12_read(
            $content,
            $certs,
            $password
        )) {
            $error = openssl_error_string();

            Log::error(
                '[SigningService] Failed to read PFX certificate',
                [
                    'openssl_error' => $error,
                ]
            );

            throw new SigningException(
                'Unable to parse the signing certificate or incorrect password.'
            );
        }

        if (empty($certs['pkey'])) {
            Log::error(
                '[SigningService] No private key found in PFX certificate'
            );

            throw new SigningException(
                'Unable to extract private key from signing certificate.'
            );
        }

        $privateKey = openssl_pkey_get_private(
            $certs['pkey']
        );

        if ($privateKey === false) {
            $error = openssl_error_string();

            Log::error(
                '[SigningService] Failed to load extracted private key',
                [
                    'openssl_error' => $error,
                ]
            );

            throw new SigningException(
                'The extracted private key is invalid.'
            );
        }

        Log::info(
            '[SigningService] PFX private key loaded successfully'
        );

        $this->privateKey = $privateKey;

        return $this->privateKey;
    }
}
