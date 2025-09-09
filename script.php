<?php

// Defina o caminho onde as chaves serão salvas
$privateKeyPath = 'config/jwt/private.pem';
$publicKeyPath = 'config/jwt/public.pem';

// Gerar a chave privada
$res = openssl_pkey_new([
    "private_key_bits" => 2048, // Tamanho da chave em bits
    "private_key_type" => OPENSSL_KEYTYPE_RSA, // Tipo de chave (RSA)
]);

// Verificar se a chave foi gerada com sucesso
if (!$res) {
    echo "Erro ao gerar chave privada: " . openssl_error_string();
    exit;
}

// Exportar a chave privada para o arquivo
$privateKeyExported = openssl_pkey_export_to_file($res, $privateKeyPath);
if (!$privateKeyExported) {
    echo "Erro ao exportar chave privada: " . openssl_error_string();
    exit;
}

// Obter a chave pública correspondente
$publicKey = openssl_pkey_get_details($res);
if (!$publicKey) {
    echo "Erro ao obter detalhes da chave pública: " . openssl_error_string();
    exit;
}

// Salvar a chave pública em um arquivo
file_put_contents($publicKeyPath, $publicKey["key"]);

// Exibir sucesso
echo "Chaves geradas com sucesso! Chave privada salva em: $privateKeyPath e chave pública salva em: $publicKeyPath";

?>
