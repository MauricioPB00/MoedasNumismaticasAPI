const express = require("express");
const fetch = require("node-fetch");
const fs = require("fs-extra");
require("dotenv").config();
const path = require("path");

const app = express();
const PORT = 3000;
const API_KEY = process.env.NUMISTA_API_KEY;

const caminhoMoedas = path.join(__dirname, "moedas.json");
const pastaImagens = path.join(__dirname, "imagens");
fs.ensureDirSync(pastaImagens);

// Configurações de download
const RETRIES = 3;
const DELAY_MS = 400;

async function sleep(ms) {
    return new Promise(r => setTimeout(r, ms));
}

// Função para baixar imagem com headers e retry
async function baixarImagem(url, caminhoArquivo) {
    for (let attempt = 1; attempt <= RETRIES; attempt++) {
        try {
            const response = await fetch(url, {
                headers: {
                    "User-Agent":
                        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
                    "Referer": "https://en.numista.com/",
                    "Accept":
                        "image/avif,image/webp,image/apng,image/*,*/*;q=0.8",
                },
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const buffer = await response.buffer();
            await fs.outputFile(caminhoArquivo, buffer);
            console.log(`✅ Imagem salva: ${caminhoArquivo}`);
            return;
        } catch (err) {
            console.warn(`⚠️ Tentativa ${attempt} falhou (${url}): ${err.message}`);
            if (attempt < RETRIES) await sleep(1000 * attempt);
            else console.error(`❌ Falha ao salvar imagem ${url}: ${err.message}`);
        }
    }
}

app.get("/", (req, res) => {
    res.send("API de moedas está rodando ✅");
});

// Rota: Buscar moedas brasileiras diretamente da Numista
app.get("/moedas/brasil", async (req, res) => {
    try {
        const page = req.query.page || 2;
        const count = req.query.count || 50;

        // Buscar código do emissor Brasil
        const urlIssuers = `https://api.numista.com/v3/issuers?lang=pt`;
        const responseIssuers = await fetch(urlIssuers, {
            headers: { "Numista-API-Key": API_KEY },
        });
        if (!responseIssuers.ok) {
            const err = await responseIssuers.json();
            return res.status(responseIssuers.status).json({ error: err });
        }
        const dataIssuers = await responseIssuers.json();

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

        // Adiciona filtro de data se informado na query
        const { data: dataFiltro, year_min, year_max } = req.query;

        let url = `https://api.numista.com/v3/types?category=coin&issuer=${issuerCode}&count=${count}&page=${   }`;

        if (dataFiltro) url += `&date=${dataFiltro}`;
        if (year_min) url += `&year_min=${year_min}`;
        if (year_max) url += `&year_max=${year_max}`;


        const response = await fetch(url, {
            headers: { "Numista-API-Key": API_KEY },
        });
        if (!response.ok) {
            const err = await response.json();
            return res.status(response.status).json({ error: err });
        }
        const data = await response.json();

        // Salvar moedas no moedas.json
        await fs.outputJson(caminhoMoedas, data.types, { spaces: 2 });

        // Baixar imagens com delay
        for (const moeda of data.types) {
            if (moeda.obverse_thumbnail) {
                const nomeArquivo = path.join(pastaImagens, `${moeda.id}_obverse.jpg`);
                await baixarImagem(moeda.obverse_thumbnail, nomeArquivo);
                await sleep(DELAY_MS);
            }
            if (moeda.reverse_thumbnail) {
                const nomeArquivo = path.join(pastaImagens, `${moeda.id}_reverse.jpg`);
                await baixarImagem(moeda.reverse_thumbnail, nomeArquivo);
                await sleep(DELAY_MS);
            }
        }

        res.json({ moedas: data.types, total: data.count });
    } catch (error) {
        console.error("❌ Erro geral:", error.message);
        res.status(500).json({ error: "Erro interno no servidor" });
    }
});

app.listen(PORT, () => {
    console.log(`🚀 Servidor rodando em http://localhost:${PORT}`);
});
