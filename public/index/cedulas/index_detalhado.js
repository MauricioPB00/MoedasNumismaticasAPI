const fetch = require("node-fetch");
const fs = require("fs-extra");
const path = require("path");
require("dotenv").config();

const API_KEY = process.env.NUMISTA_API_KEY;

const caminhoCedulas = path.join(__dirname, "cedulas.json");
const caminhoCedulasDetalhadas = path.join(__dirname, "cedulas_detalhadas.json");
const pastaImagens = path.join(__dirname, "imagens_cedulas");

// ------------------ Funções utilitárias ------------------ //

async function carregarJSON(caminho) {
  try {
    if (await fs.pathExists(caminho)) {
      return await fs.readJson(caminho);
    }
    return [];
  } catch (err) {
    console.error(`❌ Erro ao ler ${caminho}:`, err.message);
    return [];
  }
}

async function salvarJSON(caminho, dados) {
  try {
    await fs.outputJson(caminho, dados, { spaces: 2 });
  } catch (err) {
    console.error(`❌ Erro ao salvar ${caminho}:`, err.message);
  }
}

// ------------------ Baixar imagem ------------------ //

async function baixarImagem(url, caminhoArquivo) {
  try {
    if (!url) return;
    if (await fs.pathExists(caminhoArquivo)) {
      console.log(`⏩ Já existe: ${caminhoArquivo}`);
      return;
    }

    const response = await fetch(url, {
      headers: {
        "User-Agent":
          "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
      },
    });

    if (!response.ok) throw new Error(`Erro HTTP ${response.status}`);

    const buffer = await response.buffer();
    await fs.outputFile(caminhoArquivo, buffer);
    console.log(`✅ Imagem salva: ${caminhoArquivo}`);
  } catch (err) {
    console.error(`❌ Erro ao salvar imagem (${url}): ${err.message}`);
  }
}

// ------------------ Buscar detalhes das cédulas ------------------ //

async function buscarDetalhesCedulas() {
  try {
    await fs.ensureDir(pastaImagens);

    const cedulasBase = await carregarJSON(caminhoCedulas);
    if (!cedulasBase.length) {
      console.log("⚠️ Nenhuma cédula encontrada em cedulas.json");
      return;
    }

    let cedulasDetalhadas = await carregarJSON(caminhoCedulasDetalhadas);
    const idsExistentes = new Set(cedulasDetalhadas.map((c) => c.id));
    const cedulasParaBuscar = cedulasBase.filter((c) => !idsExistentes.has(c.id));

    console.log(`📘 Total: ${cedulasBase.length}`);
    console.log(`✅ Já detalhadas: ${cedulasDetalhadas.length}`);
    console.log(`➡️  Faltam buscar: ${cedulasParaBuscar.length}`);
    console.log("-----------------------------------------------------");

    for (const cedula of cedulasParaBuscar) {
      const url = `https://api.numista.com/v3/types/${cedula.id}?lang=pt`;

      try {
        const resp = await fetch(url, {
          headers: { "Numista-API-Key": API_KEY },
        });

        if (!resp.ok) {
          console.error(`❌ Erro ao buscar ID ${cedula.id}: ${resp.status}`);
          continue;
        }

        const detalhe = await resp.json();
        cedulasDetalhadas.push(detalhe);
        console.log(`✅ Detalhe salvo: ${cedula.id} - ${detalhe.title}`);

        // Salva progresso
        await salvarJSON(caminhoCedulasDetalhadas, cedulasDetalhadas);

        // ------------------ Baixar imagens ------------------ //
        const nomeLimpo = detalhe.title
          .replace(/[\\\/:*?"<>|]/g, "")
          .replace(/\s+/g, "_");

        if (detalhe.obverse?.picture) {
          const caminhoObverse = path.join(
            pastaImagens,
            `${cedula.id}_${nomeLimpo}_obverse.jpg`
          );
          await baixarImagem(detalhe.obverse.picture, caminhoObverse);
        }

        if (detalhe.reverse?.picture) {
          const caminhoReverse = path.join(
            pastaImagens,
            `${cedula.id}_${nomeLimpo}_reverse.jpg`
          );
          await baixarImagem(detalhe.reverse.picture, caminhoReverse);
        }

        // Delay entre chamadas
        await new Promise((r) => setTimeout(r, 1000));
      } catch (err) {
        console.error(`❌ Erro ao processar ID ${cedula.id}:`, err.message);
      }
    }

    console.log("-----------------------------------------------------");
    console.log(`🏁 Concluído! Total detalhadas: ${cedulasDetalhadas.length}`);
  } catch (err) {
    console.error("❌ Erro geral:", err.message);
  }
}

// ------------------ Executar ------------------ //

buscarDetalhesCedulas();
