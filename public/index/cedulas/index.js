const express = require("express");
const fetch = require("node-fetch");
const fs = require("fs-extra");
require("dotenv").config();
const path = require("path");

const app = express();
const PORT = 3000;
const API_KEY = process.env.NUMISTA_API_KEY;

const caminhoCedulas = path.join(__dirname, "cedulas.json");
const pastaImagensCedulas = path.join(__dirname, "imagens_cedulas");

// Função para baixar imagens diretamente
async function baixarImagem(url, caminhoArquivo) {
  try {
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

app.get("/", (req, res) => {
  res.send("API de cédulas brasileiras está rodando ✅");
});

// Rota para buscar cédulas brasileiras
app.get("/cedulas/brasil", async (req, res) => {
  try {
    const page = req.query.page || 1;
    const count = req.query.count || 50;

    // Buscar o código correto do emissor Brasil
    const urlIssuers = `https://api.numista.com/v3/issuers?lang=pt`;
    const responseIssuers = await fetch(urlIssuers, {
      headers: { "Numista-API-Key": API_KEY },
    });

    if (!responseIssuers.ok) {
      const err = await responseIssuers.json();
      return res.status(responseIssuers.status).json({ error: err });
    }

    const dataIssuers = await responseIssuers.json();

    // Encontrar o código do Brasil
    const brasil = dataIssuers.issuers.find((i) => {
      const nome = i.name.toLowerCase();
      return (
        nome.includes("brasil") ||
        nome.includes("brazil") ||
        nome.includes("república federativa do brasil")
      );
    });

    if (!brasil) {
      return res.status(404).json({ error: "Emissor Brasil não encontrado" });
    }

    const issuerCode = brasil.code;

    // Filtro opcional por data
    const dataFiltro = req.query.data;
    const url = `https://api.numista.com/v3/types?category=banknote&issuer=${issuerCode}&count=${count}&page=${page}${dataFiltro ? `&date=${dataFiltro}` : ""
      }`;

    const response = await fetch(url, {
      headers: { "Numista-API-Key": API_KEY },
    });

    if (!response.ok) {
      const err = await response.json();
      return res.status(response.status).json({ error: err });
    }

    const data = await response.json();

    // Salvar cédulas no arquivo JSON
    let cedulasExistentes = [];
    if (await fs.pathExists(caminhoCedulas)) {
      cedulasExistentes = await fs.readJson(caminhoCedulas);
    }

    // Mesclar e remover duplicados pelo ID
    const mergedCedulas = [...cedulasExistentes, ...data.types];
    const uniqueCedulas = mergedCedulas.filter(
      (v, i, a) => a.findIndex(t => t.id === v.id) === i
    );

    // Salvar no JSON
    await fs.outputJson(caminhoCedulas, uniqueCedulas, { spaces: 2 });

    // Baixar imagens das cédulas
    for (const cedula of data.types) {
      if (cedula.obverse_thumbnail) {
        const nomeArquivo = path.join(pastaImagensCedulas, `${cedula.id}_obverse.jpg`);
        await baixarImagem(cedula.obverse_thumbnail, nomeArquivo);
      }

      if (cedula.reverse_thumbnail) {
        const nomeArquivo = path.join(pastaImagensCedulas, `${cedula.id}_reverse.jpg`);
        await baixarImagem(cedula.reverse_thumbnail, nomeArquivo);
      }
    }

    res.json({ cedulas: data.types, total: data.count });
  } catch (error) {
    console.error("❌ Erro interno:", error);
    res.status(500).json({ error: "Erro interno no servidor" });
  }
});

app.listen(PORT, () => {
  console.log(`🚀 Servidor rodando em http://localhost:${PORT}`);
});
