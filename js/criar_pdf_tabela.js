let geracaoBloqueada = false;

async function gerarPDFFullFunction(esse) {
    $(esse).attr("class",'fa-solid fa-spinner fa-spin-pulse')
  if (geracaoBloqueada) {
    alert("Você só pode gerar um PDF a cada 10 segundos!");
    return;
  }

  $("tbody tr td").each(function () {
    $(this).text(" - " + $(this).text());
  });

  try {
    await gerarPDFAsync();
  } catch (erro) {
    console.error("Erro ao gerar o PDF:", erro);
  }

  $("tbody tr td").each(function () {
    $(this).text($(this).text().replace(" - ", ""));
  });
  $(esse).attr("class",'fa-regular fa-file-pdf')
  geracaoBloqueada = true;
  await aguardar(10000);
  geracaoBloqueada = false;
}

async function gerarPDFAsync() {
  const table = document.getElementById("table_tabela");
  console.log("AQUI::", table);

  const opt = {
    margin: 10,
    filename: "minha_tabela.pdf",
    image: { type: "jpeg", quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
  };

  try {
    await html2pdf().from(table).set(opt).save();
  } catch (erro) {
    console.error("Erro ao gerar o PDF:", erro);
    throw erro; 
  }
}

function aguardar(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}
