<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\String\UnicodeString;

//Encrypt/Decrypt values in certain column(s) with provided keys, using asymmetric encryption

// --- Pasul 1: Generarea cheilor ---

// Configurare pentru generarea unei noi perechi de chei RSA
$config = [
    "digest_alg" => "sha512",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
];

// Creeaza resursa pentru noua pereche de chei
$privateKeyResource = openssl_pkey_new($config);

// Extrage cheia privata ca string
openssl_pkey_export($privateKeyResource, $privateKey);

// Extrage cheia publica ca string
$publicKeyDetails = openssl_pkey_get_details($privateKeyResource);
$publicKey = $publicKeyDetails["key"];

echo "--- Cheile au fost generate ---" . PHP_EOL;
// echo "Cheia Publica:\n" . $publicKey . PHP_EOL; // Decomenteaza pentru a vedea cheile
// echo "Cheia Privata:\n" . $privateKey . PHP_EOL; // Decomenteaza pentru a vedea cheile

// --- Pasul 2: Criptarea ---

$messageToEncrypt = 'Valoare secreta din fisierul CSV.';
$encryptedData = '';

echo "Mesaj Original: " . $messageToEncrypt . PHP_EOL;

// Cripteaza datele folosind cheia publica
openssl_public_encrypt($messageToEncrypt, $encryptedData, $publicKey);

// Afisam datele criptate folosind Base64 pentru a le face lizibile
echo "Date Criptate (Base64): " . base64_encode($encryptedData) . PHP_EOL;

// --- Pasul 3: Decriptarea ---

$decryptedData = '';

// Decripteaza datele folosind cheia privata corespunzatoare
openssl_private_decrypt($encryptedData, $decryptedData, $privateKey);

echo "Date Decriptate: " . $decryptedData . PHP_EOL;
