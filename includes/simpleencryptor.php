<?php
    /*
        // Example usage
        $encryptor = new SimpleEncryptor();

        // Encrypt and decrypt a string
        $encryptedString = $encryptor->encrypt('Hello, World!');
        echo 'Encrypted String: ' . $encryptedString . PHP_EOL;
        echo 'Decrypted String: ' . $encryptor->decrypt($encryptedString) . PHP_EOL;
    */
    class SimpleEncryptor {
        private $cipher;
        private $key;
        private $options;
        private $iv;

        public function __construct($key = 'socialself', $cipher = 'aes-256-cbc', $options = 0) {
            if(!in_array($cipher, openssl_get_cipher_methods())) {
                throw new Exception('Cipher not found');
            }

            $this->cipher = $cipher;
            $this->key = $key;
            $this->options = $options;
            $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        }

        public function encrypt($data) {
            // If data is an array, convert it to JSON string
            if(is_array($data)) {
                $data = json_encode($data);
            } else {
                // Convert other types to string
                $data = (string)$data;
            }

            $encryptedData = openssl_encrypt($data, $this->cipher, $this->key, $this->options, $this->iv);
            return base64_encode($this->iv . $encryptedData);
        }

        public function decrypt($encryptedData) {
            $encryptedData = base64_decode($encryptedData);
            $ivLength = openssl_cipher_iv_length($this->cipher);
            $iv = substr($encryptedData, 0, $ivLength);
            $encryptedData = substr($encryptedData, $ivLength);

            $data = openssl_decrypt($encryptedData, $this->cipher, $this->key, $this->options, $iv);

            // Try to decode JSON, if fails return as string
            $decodedData = json_decode($data, true);
            if(json_last_error() == JSON_ERROR_NONE) {
                return $decodedData;
            }

            return $data;
        }
    }
?>
