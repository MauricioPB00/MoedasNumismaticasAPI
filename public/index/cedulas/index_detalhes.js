const fetch = require("node-fetch");
const fs = require("fs-extra");
const path = require("path");
require("dotenv").config();

const API_KEY = process.env.NUMISTA_API_KEY;

const caminhoCedulas = path.join(__dirname, "cedulas.json");
const caminhoCedulasDetalhadas = path.join(__dirname, "cedulas_detalhadas.json");

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

async function buscarDetalhesCedulas() {
  try {
    const cedulasBase = await carregarJSON(caminhoCedulas);
    if (!cedulasBase.length) {
      console.log("⚠️ Nenhuma cédula encontrada em cedulas.json");
      return;
    }

    let cedulasDetalhadas = await carregarJSON(caminhoCedulasDetalhadas);

    const idsExistentes = new Set(cedulasDetalhadas.map((c) => c.id));
    const cedulasParaBuscar = cedulasBase.filter((c) => !idsExistentes.has(c.id));

    console.log(`📘 Total de cédulas no arquivo base: ${cedulasBase.length}`);
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

        // Salva progresso a cada item
        await salvarJSON(caminhoCedulasDetalhadas, cedulasDetalhadas);

        // Delay para evitar limite da API
        await new Promise((r) => setTimeout(r, 1000));
      } catch (err) {
        console.error(`❌ Erro ao processar ID ${cedula.id}:`, err.message);
      }
    }

    console.log("-----------------------------------------------------");
    console.log(`🏁 Processo concluído! Total detalhadas: ${cedulasDetalhadas.length}`);
  } catch (err) {
    console.error("❌ Erro geral:", err.message);
  }
}

// Executar
buscarDetalhesCedulas();
