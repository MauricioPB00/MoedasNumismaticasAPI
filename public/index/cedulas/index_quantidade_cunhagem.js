// fetch_issues_node_adjusted.js
// Uso: NUMISTA_API_KEY=xxx node fetch_issues_node_adjusted.js

const fs = require("fs-extra");
const path = require("path");
const fetch = require("node-fetch");
require("dotenv").config();

const API_KEY = process.env.NUMISTA_API_KEY;
if (!API_KEY) {
  console.error("❌ Defina NUMISTA_API_KEY no .env");
  process.exit(1);
}

const caminhoCedulas = path.join(__dirname, "cedulas.json");
const caminhoIssues = path.join(__dirname, "cedulas_issues.json");
const DEFAULT_LANG = "pt";

// Carregar JSON
async function carregarJSON(caminho) {
  if (await fs.pathExists(caminho)) {
    return await fs.readJson(caminho);
  }
  return [];
}

// Salvar JSON
async function salvarJSON(caminho, dados) {
  await fs.outputJson(caminho, dados, { spaces: 2 });
}

// Função fetch com retries
async function retryFetch(url, options = {}, retries = 3, backoff = 800) {
  let attempt = 0;
  while (attempt < retries) {
    try {
      const resp = await fetch(url, options);
      if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
      return resp.json();
    } catch (err) {
      attempt++;
      console.warn(`⚠️ Fetch falhou (${attempt}/${retries}) ${url}: ${err.message}`);
      if (attempt >= retries) throw err;
      await new Promise(r => setTimeout(r, backoff * attempt));
    }
  }
}

// Buscar issues de um type_id
async function buscarIssues(typeId) {
  const url = `https://api.numista.com/v3/types/${typeId}/issues?lang=${DEFAULT_LANG}`;
  console.log(`🔍 Buscando issues para type_id ${typeId}`);
  const issues = await retryFetch(url, {
    headers: { "Numista-API-Key": API_KEY }
  });

  // Adiciona type_id original em cada issue
  if (Array.isArray(issues)) {
    return issues.map(issue => ({ ...issue, type_id: typeId }));
  }
  return [];
}

// Main
(async () => {
  try {
    const cedulas = await carregarJSON(caminhoCedulas);
    let todasIssues = [];

    for (const cedula of cedulas) {
      if (!cedula.id) continue;
      const issues = await buscarIssues(cedula.id);
      todasIssues.push(...issues);
    }

    // Remover duplicados pelo ID da issue
    const uniqueIssues = todasIssues.filter((v, i, a) => a.findIndex(t => t.id === v.id) === i);

    await salvarJSON(caminhoIssues, uniqueIssues);
    console.log(`✅ Finalizado! Total de issues salvas: ${uniqueIssues.length}`);
  } catch (err) {
    console.error("❌ Erro geral:", err);
  }
})();
