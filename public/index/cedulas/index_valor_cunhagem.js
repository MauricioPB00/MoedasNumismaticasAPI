// fetch_prices_node.js
// Uso: NUMISTA_API_KEY=xxx node fetch_prices_node.js

const fs = require("fs-extra");
const path = require("path");
const fetch = require("node-fetch");
require("dotenv").config();

const API_KEY = process.env.NUMISTA_API_KEY;
if (!API_KEY) {
  console.error("❌ Defina NUMISTA_API_KEY no .env");
  process.exit(1);
}

const caminhoIssues = path.join(__dirname, "cedulas_issues.json");
const caminhoPrices = path.join(__dirname, "cedulas_prices.json");
const DEFAULT_CURRENCY = "EUR";
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

// Buscar preços de uma issue
async function buscarPrecos(typeId, issueId) {
  const url = `https://api.numista.com/v3/types/${typeId}/issues/${issueId}/prices?currency=${DEFAULT_CURRENCY}&lang=${DEFAULT_LANG}`;
  console.log(`💰 Buscando preços para type_id ${typeId} / issue_id ${issueId}`);
  const data = await retryFetch(url, {
    headers: { "Numista-API-Key": API_KEY }
  });

  // Adicionar type_id original e issue_id
  return {
    type_id: typeId,
    issue_id: issueId,
    currency: data.currency,
    prices: data.prices || []
  };
}

// Main
(async () => {
  try {
    const issues = await carregarJSON(caminhoIssues);
    let todasPrices = [];

    for (const issue of issues) {
      if (!issue.type_id || !issue.id) continue;
      const preco = await buscarPrecos(issue.type_id, issue.id);
      todasPrices.push(preco);

      console.log(`   ✅ Encontrado ${preco.prices.length} preços para issue_id ${issue.id}`);
    }

    await salvarJSON(caminhoPrices, todasPrices);
    console.log(`✅ Finalizado! Total de issues com preços salvos: ${todasPrices.length}`);
  } catch (err) {
    console.error("❌ Erro geral:", err);
  }
})();
