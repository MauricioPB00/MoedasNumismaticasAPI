// fetch_prices_node.js
// Uso: NUMISTA_API_KEY=xxx node fetch_prices_node.js


// ele vai salvando no json e REMOVENDO o outro 
// mais de 20.000 requi ( 2 contas )
// salve o json moedas_issues.json


const fs = require("fs-extra");
const path = require("path");
const fetch = require("node-fetch");
require("dotenv").config();

const API_KEY = process.env.NUMISTA_API_KEY;
if (!API_KEY) {
  console.error("❌ Defina NUMISTA_API_KEY no .env");
  process.exit(1);
}

const caminhoIssues = path.join(__dirname, "moedas_issues.json");
const caminhoPrices = path.join(__dirname, "moedas_prices.json");
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
const REQUEST_DELAY_MS = 500;
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}
// Buscar preços de uma issue
async function buscarPrecos(typeId, issueId) {
  await sleep(REQUEST_DELAY_MS);
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
    let issues = await carregarJSON(caminhoIssues);
    let todasPrices = [];

    // Carregar preços já existentes
    if (await fs.pathExists(caminhoPrices)) {
      todasPrices = await carregarJSON(caminhoPrices);
    }

    const DELAY_MS = 500;
    const MAX_ISSUE_RETRIES = 3;

    while (issues.length > 0) {
      const issue = issues[0]; // pegar a primeira issue da lista

      if (!issue.type_id || !issue.id) {
        // remover issue inválida
        issues.shift();
        await salvarJSON(caminhoIssues, issues);
        continue;
      }

      let attempt = 0;
      let sucesso = false;

      while (attempt < MAX_ISSUE_RETRIES && !sucesso) {
        try {
          const preco = await buscarPrecos(issue.type_id, issue.id);
          todasPrices.push(preco);

          // Salvar preços incrementalmente
          await salvarJSON(caminhoPrices, todasPrices);

          console.log(`   ✅ Encontrado ${preco.prices.length} preços para issue_id ${issue.id}`);

          // Remover a issue processada e salvar issues restantes
          issues.shift();
          await salvarJSON(caminhoIssues, issues);

          sucesso = true;
        } catch (err) {
          attempt++;
          console.warn(`⚠️ Tentativa ${attempt}/${MAX_ISSUE_RETRIES} falhou para issue_id ${issue.id}: ${err.message}`);
          if (attempt >= MAX_ISSUE_RETRIES) {
            console.error(`❌ Falha definitiva na issue_id ${issue.id}, pulando...`);
            // mesmo assim removemos do arquivo para não travar o loop
            issues.shift();
            await salvarJSON(caminhoIssues, issues);
          } else {
            await sleep(DELAY_MS * attempt);
          }
        }
      }

      await sleep(DELAY_MS);
    }

    console.log(`✅ Finalizado! Total de issues com preços salvos: ${todasPrices.length}`);
  } catch (err) {
    console.error("❌ Erro geral:", err);
  }
})();

