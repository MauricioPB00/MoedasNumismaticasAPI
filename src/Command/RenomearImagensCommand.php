<?php
// Pasta onde estão as imagens
$pastaImagens = __DIR__ . '/public/uploads/cedulas/';

// Lê todos os arquivos da pasta
$arquivos = scandir($pastaImagens);

foreach ($arquivos as $arquivo) {
    // Pula '.' e '..'
    if ($arquivo === '.' || $arquivo === '..') continue;

    $caminhoAntigo = $pastaImagens . $arquivo;

    // Verifica se é arquivo
    if (!is_file($caminhoAntigo)) continue;

    // Regex para identificar ID e obverse/reverse
    if (preg_match('/^(\d+).*_(obverse|reverse)\.jpg$/i', $arquivo, $matches)) {
        $id = $matches[1];
        $lado = strtolower($matches[2]);

        $novoNome = "{$id}_{$lado}.jpg";
        $caminhoNovo = $pastaImagens . $novoNome;

        // Renomeia
        if (!file_exists($caminhoNovo)) {
            rename($caminhoAntigo, $caminhoNovo);
            echo "✅ $arquivo → $novoNome\n";
        } else {
            echo "⚠️ Já existe: $novoNome, pulando\n";
        }
    }
}
