function resetPedido() {
  formatoDataHora = function (data) {
    var dia = ('0' + data.getDate()).slice(-2);
    var mes = ('0' + (data.getMonth() + 1)).slice(-2);
    var ano = data.getFullYear();
    var horas = ('0' + data.getHours()).slice(-2);
    var minutos = ('0' + data.getMinutes()).slice(-2);
    return ano + '-' + mes + '-' + dia + 'T' + horas + ':' + minutos;
  };
$(".modal_anotar_pedido input[type='text']").each(function(){
if($(this).attr("id") == "quantidade_produto_pedido") {
$(this).val(1)
}else{
$(this).val("")

}
})
$(".modal_anotar_pedido tbody").children().remove()
var dataAtual = new Date();
 var dataFutura = new Date();
  dataFutura.setMinutes(dataFutura.getMinutes() + 30);
$('#data_pedido').val(formatoDataHora(dataAtual))
$('#data_entrega').val(formatoDataHora(dataFutura))
$("#finaliza_sangria_button").text("Finalizar Operação")
$("#finaliza_sangria_button").removeAttr("disabled")
$(".modal").css("display",'none')
$("fundo").css("display",'none')
}
$(".datas").change(function () {
  alterarTabela();
});
function setCaixa(code) {
  data = {
    colaborador: code,
    blue_sky: true,
  };
  let caixa_retornado = "";
  $.post("Models/post_receivers/select_colaborador.php", data, function (ret) {
    console.log(ret);
    caixa_retornado = ret;
  });
  return caixa_retornado;
}
$("#imprimir_ultima_venda").click(function () {
  $("#imprimir_ultima_venda i").css("display", "block");
  $("#imprimir_ultima_venda span").css("display", "none");
  $.post("Models/post_receivers/select_ultima_venda.php", {}, function (e) {
    console.log(e);
    $("#imprimir_ultima_venda i").css("display", "none");
    $("#imprimir_ultima_venda span").css("display", "block");
  });
});
$("#add_produto_opener").click(function () {
  $(".modal_produtos").css("display", "none");
  data = {};
  $.post("../Models/post_receivers/gerar_codigos.php", data, function (ret) {
    console.log(ret);
    let res = JSON.parse(ret);
    $("#codigo_barras_produto_add").val(res.codigo);
    $("#codigo_produto_add").val(res.codigo_id);
  });
});
$(".modal_adicionar_produto").submit(function (e) {
  e.preventDefault();
  data = {
    nome: $("#nome_produto_add").val(),
    codigo: $("#codigo_barras_produto_add").val(),
    codigo_id: $("#codigo_produto_add").val(),
    preco: $("#preco_produto_add").val(),
    por_peso: $('input[name="produto_por_peso"]:checked').val(),
  };
  $.post("../Models/post_receivers/insert_produto.php", data, function (ret) {
    if (ret.includes("Codigo_repetido")) {
      alert("Codigo do produto já existente");
    } else if (ret.includes("Codigo_barras_repetido")) {
      alert("Codigo de barras do produto já existente");
    } else {
      location.reload();
    }
  });
});

$(".modal_adicionar_produto").submit(function () {});

$(".modal_funcionarios").submit(function (e) {
  e.preventDefault();
  data = {
    adm: $('input[name="add_funcionario"]:checked').val(),
    nome: $("#input_add_usuario_nome").val(),
    codigo: $("#input_add_usuario_codigo").val(),
    caixa: $("#select_caixa_add_usuario").val(),
  };
  $.post(
    "../Models/post_receivers/insert_colaborador.php",
    data,
    function (ret) {
      console.log(ret);
      if (ret != "ERROR") {
        $(".modal_adicionar_produto input").each(function () {
          console.log($(this).val());
        });
        location.reload();
      } else {
        alert("Código já cadastrado");
      }
    }
  );
});
$("#adicionar_caixa").submit(function (e) {
  e.preventDefault();
  data = {
    nome_caixa: $("#nome_caixa").val(),
    troco_inicial: $("#troco_inicial").val(),
  };

  $.post("../Models/post_receivers/insert_caixa.php", data, function (ret) {
    location.reload();
  });
});
let add_caixa_opnd = false;
$("#add_caixa_opener").click(function () {
  if (add_caixa_opnd) {
    $("#adicionar_caixa").css("display", "none");
  } else {
    $("#adicionar_caixa").css("display", "flex");
  }
  add_caixa_opnd = !add_caixa_opnd;
});
$("#form_equip").submit(function (e) {
  e.preventDefault();
  data = {
    caixa: $("#select_caixa").val(),
    impressora: $("#nome_impressora").val(),
    porta_balanca: $("#porta_balanca").val(),
    freq_balanca: $("#freq_balanca").val(),
  };

  $.post(
    "../Models/post_receivers/insert_equipamentos.php",
    data,
    function (ret) {
      location.reload();
    }
  );
});
let confirmou = false;
$("#caixa_remover").click(function () {
  if (confirmou) {
    data = {
      caixa: $("#select_caixa").val(),
    };

    $.post("../Models/post_receivers/delete_caixa.php", data, function (ret) {
      location.reload();
    });
  } else {
    $(this).text("Tem certeza?");
  }
  confirmou = true;
});
$("#select_caixa").change(function () {
  $("#form_equip").css("display", "block");
  $("#caixa_remover").css("display", "block");

  $("#form_equip red").text($(this).val() + ":");
  if ($(this).val() == "todos") {
    $("#form_equip").css("display", "none");
    $("#caixa_remover").css("display", "none");
  }
  data = {
    caixa: $(this).val(),
  };

  $.post(
    "../Models/post_receivers/select_equipamentos.php",
    data,
    function (ret) {
      let infos = JSON.parse(ret);
      console.log(infos);
      $("#nome_impressora").val(infos.impressora);
      $("#porta_balanca").val(infos.porta_balanca);
      $("#freq_balanca").val(infos.velocidade_balanca);
    }
  );
});
$(".modal_funcionarios .fa-trash-can").click(function () {
  data = {
    id: $(this).attr("pessoa"),
  };
  $.post(
    "../Models/post_receivers/delete_colaborador.php",
    data,
    function (ret) {
      location.reload();
    }
  );
});
$("#salvar_caixa").click(function () {
  data = {
    caixa: $("#caixa_selecionado").val(),
    no_permission: true,
  };
  $.post("Models/post_receivers/select_caixa.php", data, function (ret) {
    location.reload();
  });
});
$(".modal_anotar_pedido").submit(function (e) {
  if($(this).attr('fazendo_pedido')){
    return false
  }
  $(this).attr("fazendo_pedido", true);
  
  $("#finaliza_sangria_button").html(
    '<i class="fa-solid fa-spinner fa-spin-pulse"></i>'
  );
  e.preventDefault();
  produtos = [];
  data_entrega = $("#data_entrega").val().replace("T", " ");
  data_pedido = $("#data_pedido").val().replace("T", " ");
  $(".modal_anotar_pedido tbody")
    .children()
    .each(function (index) {
      let produto = {
        id: $(this).attr("produto"),
        quantidade: $(this).attr("quantidade"),
        preco: $(this).attr("preco_produto"),
      };
      produtos[index] = produto;
    });

  console.log($("#data_pedido").val());
  data = {
    pedido: $("#pedido_id").val(),
    path: $("#include_path").val(),
    caixa: setCaixa($("#codigo_colaborador_input").val()),
    endereco: $("#endereco_cliente_input").val(),
    pagamento: $("#metodo_pagamento").val(),
    produtos: produtos,
    cliente: $("#nome_cliente_input").val(),
    numero_cliente: $("#numero_cliente_input").val(),
    data_entrega: data_entrega,
    data_pedido: data_pedido,
    codigo_colaborador: $("#codigo_colaborador_input").val(),
    valor_entrada: $("#valor_entrada").val(),
    metodo_entrada: $("#metodo_pagamento_entrada")
      .val()
      .replace(".", "")
      .replace(",", "."),
    retirada: $('input[name="entrega_retirada"]:checked').val(),
  };
  if ($("#editando").val() == "true") {
    $.post("Models/post_receivers/update_pedido.php", data, function (ret) {
      console.log(ret);
      $(this).attr("fazendo_pedido", false);
      resetPedido()
    });
  } else {
    $.post("Models/post_receivers/insert_pedido.php", data, function (ret) {
      console.log(ret);
      $(this).attr("fazendo_pedido", false);
      
      resetPedido()
    });
  }
});
$.post(
  "Models/post_receivers/select_pedidos.php",
  { anytime: true },
  function (ret) {
    let pedidos = JSON.parse(ret);
    if (!$.isEmptyObject(pedidos)) {
      pedidos.forEach((element) => {
        let data = element.data_entrega.split(" ");
        $(".lista_pedidos").append(
          "<span > <input class='pedido_feito' type='checkbox' pedido='" +
            element.id +
            "'><label onclick='editarPedido(this)' pedido='" +
            JSON.stringify(element) +
            "'>" +
            element.cliente +
            "-" +
            element.data_entrega +
            " </label></span>"
        );
        $(".pedido_feito").change(function () {
          let pedido = $(this).attr("pedido");
          data = {
            pedido: pedido,
          };
          $.post(
            "Models/post_receivers/update_pedido_feito.php",
            data,
            function (ret) {
              location.reload();
            }
          );
        });
      });
    }
  }
);
function editarPedido(esse) {
  // Exibir o fundo e a modal
  console.log(esse);
  $(".modal_anotar_pedido tbody").empty();
  exibirModalAnotarPedido();
  $("#editando").val("true");
  console.log($(esse).attr("pedido"));
  let pedido = JSON.parse($(esse).attr("pedido"));
  $("#pedido_id").val(pedido.id);
  // Preencher os campos da modal com os dados do pedido
  $("#nome_cliente_input").val(pedido.cliente);
  $("#endereco_cliente_input").val(pedido.endereco);
  $("#metodo_pagamento").val(pedido.forma_pagamento);
  $("#data_pedido").val(pedido.data_pedido);
  $("#data_entrega").val(pedido.data_entrega);
  $("#valor_entrada").val(pedido.valor_entrada);
  $("#numero_cliente_input").val(pedido.numero_cliente);

  $("#metodo_pagamento_entrada").val(pedido.metodo_entrada);

  let produtos = JSON.parse(pedido.produtos);
  produtos.forEach((produto) => {
    // Construir a linha da tabela da modal com as informações do produto
    const newRow = `
        <tr preco_produto="${produto.preco
          .toString()
          .replace(",", ".")}" produto="${produto.id}" quantidade="${$(
      "#quantidade_produto_pedido"
    ).val()}" class="produto_pedido${produto.id}">
          <td>${$("#quantidade_produto_pedido").val()}</td>
          <td>${produto.id}</td>
          <td><input value='${
            produto.preco
          }' type='text'class='oders_inputs input_valor_pedido_produto' produto='${produto.id.replace(
      " ",
      "_"
    )}' onKeyUp='mascaraMoeda(this, event)' id='preco_produto_${produto.id.replace(
      " ",
      "_"
    )}'></td>
          <td id='valor_produto_total_${produto.id}'>R$ ${(
      parseFloat(produto.preco.replace(",", ".")) *
      parseFloat(produto.quantidade)
    )
      .toFixed(2)
      .toString()
      .replace(".", ",")}</td>
          <td produto="${produto.id}" class="remove_item_pedido">-</td>
        </tr>
      `;

    $(".modal_anotar_pedido tbody").append(newRow);
    $(".remove_item_pedido").click(function () {
      $(".produto_pedido" + $(this).attr("produto")).remove();
    });

    $(".input_valor_pedido_produto").keyup(function () {
      let valor_produto = parseFloat(
        $(this).val().replace(".", "").replace(",", ".")
      );
      let produto = $(this).attr("produto");
      let novoValor =
        valor_produto * $(".produto_pedido" + produto).attr("quantidade");
      const options = {
        style: "currency",
        currency: "BRL",
        minimumFractionDigits: 2,
        maximumFractionDigits: 5,
      };
      $(".produto_pedido" + produto).attr(
        "preco_produto",
        novoValor.toFixed(2)
      );
      $("#valor_produto_total_" + produto).text(
        new Intl.NumberFormat("pt-BR", options).format(novoValor)
      );
    });
  });
}

function exibirModalAnotarPedido() {
  $("fundo").css("display", "flex");
  $(".modal_anotar_pedido").css("display", "flex");
}
function mudarTempo(esse) {
  var dataNaTabela = moment($("#data_minima").val(), "YYYY-MM-DD");
  var dataAtual = moment();
  dataNaTabela = dataNaTabela.add(1, "days");
  if (
    dataNaTabela.format("DD/MM/YYYY") == dataAtual.format("DD/MM/YYYY") &&
    $(esse).attr("id") == "adiantar_semana"
  ) {
    $("#adiantar_semana").css("visibility", "hidden");
  } else {
    $("#adiantar_semana").css("visibility", "unset");
  }

  let dataMoment = moment($("#data_minima").val(), "YYYY-MM-DD");
  if ($(esse).attr("id") == "voltar_semana") {
    var dataNovaAtrasada = dataMoment.subtract(1, "days");

    $("#data_minima").val(dataNovaAtrasada.format("YYYY-MM-DD"));
    $("#data_maxima").val(dataNovaAtrasada.format("YYYY-MM-DD"));
  } else {
    var dataNovaAdiantada = dataMoment.add(1, "days");

    $("#data_minima").val(dataNovaAdiantada.format("YYYY-MM-DD"));
    $("#data_maxima").val(dataNovaAdiantada.format("YYYY-MM-DD"));
  }

  alterarTabela();
}
function alterarTabela() {
  data = {
    data_min: $("#data_minima").val(),
    data_max: $("#data_maxima").val(),
    caixa: $("#select_caixa").val(),
  };
  $.post("../Models/post_receivers/select_metricas.php", data, function (ret) {
    row = JSON.parse(ret);
    $(".pagamento_recorrente").text(row.formaPagamentoMaisRepetida);
    $(".quant_vendas").text(row.quantidadeVendas);
    $(".top_produto").text(row.produtoMaisVendido);
    console.log(row.totalValor);
    $(".right_subdivision .valor_total").text("R$", row.totalValor);
  });
  if ($("dot").attr("style").includes("left")) {
    data = {
      data_min: $("#data_minima").val(),
      data_max: $("#data_maxima").val(),
      caixa: $("#select_caixa").val(),
    };
    $.post(
      "../Models/post_receivers/select_vendas_periodo.php",
      data,
      function (ret) {
        $(".tabela_father tbody").html(ret);
        if ($("#data_minima").val() == $("#data_maxima").val()) {
          var novaData = moment($("#data_minima").val(), "YYYY-MM-DD");
          var novaDataFormatada = novaData.format("DD/MM/YYYY");
          $(".tabela_header span").html(
            "Vendas no dia: <yellow>" +
              novaDataFormatada +
              "</yellow> <i onclick='gerarPDFFullFunction(this)' class='gerar_pdf fa-regular fa-file-pdf'></i>"
          );
        } else {
          var dataMomentMAX = moment($("#data_maxima").val(), "YYYY-MM-DD");
          var dataMAXFormatada = dataMomentMAX.format("DD/MM/YYYY");
          var dataMomentMIN = moment($("#data_minima").val(), "YYYY-MM-DD");
          var dataMINFormatada = dataMomentMIN.format("DD/MM/YYYY");
          $(".tabela_header span").html(
            "<i class='fa-solid fa-chart-line' id='show_graphs'></i> Vendas no período de: <yellow>" +
              dataMINFormatada +
              "</yellow> até <yellow>" +
              dataMAXFormatada +
              "</yellow> <i onclick='gerarPDFFullFunction()'  class='gerar_pdf fa-regular fa-file-pdf'></i>"
          );
        }
      }
    );
  } else {
    data = {
      data_min: $("#data_minima").val(),
      data_max: $("#data_maxima").val(),
      caixa: $("#select_caixa").val(),
    };
    $.post("../Models/post_receivers/select_podium.php", data, function (ret) {
      $(".tabela_father tbody").html(ret);
      var dataMomentMAX = moment($("#data_maxima").val(), "YYYY-MM-DD");
      var dataMAXFormatada = dataMomentMAX.format("DD/MM/YYYY");
      var dataMomentMIN = moment($("#data_minima").val(), "YYYY-MM-DD");
      var dataMINFormatada = dataMomentMIN.format("DD/MM/YYYY");
      $(".tabela_header span").html(
        "<i class='fa-solid fa-chart-line' id='show_graphs'></i> Vendas no período de: <yellow>" +
          dataMINFormatada +
          "</yellow> até <yellow>" +
          dataMAXFormatada +
          "</yellow> <i onclick='gerarPDFFullFunction()'class='gerar_pdf' class='fa-regular fa-file-pdf'></i>"
      );
    });
  }
}
$("switch").click(function () {
  if ($("dot").attr("style").includes("left")) {
    $("dot").css("float", "right");
    data = {
      data_min: $("#data_minima").val(),
      data_max: $("#data_maxima").val(),
      caixa: $("#select_caixa").val(),
      switch: true,
    };
    $.post("../Models/post_receivers/select_podium.php", data, function (ret) {
      $(".tabela_father").remove();
      $("body").append(ret);
      var dataMomentMAX = moment($("#data_maxima").val(), "YYYY-MM-DD");
      var dataMAXFormatada = dataMomentMAX.format("DD/MM/YYYY");
      var dataMomentMIN = moment($("#data_minima").val(), "YYYY-MM-DD");
      var dataMINFormatada = dataMomentMIN.format("DD/MM/YYYY");
      $(".tabela_header span").html(
        "<i class='fa-solid fa-chart-line' id='show_graphs'></i> Vendas no período de: <yellow>" +
          dataMINFormatada +
          "</yellow> até <yellow>" +
          dataMAXFormatada +
          "</yellow> <i onclick='gerarPDFFullFunction()'class='gerar_pdf fa-regular fa-file-pdf'></i>"
      );
    });
  } else {
    $("dot").css("float", "left");
    data = {
      data_min: $("#data_minima").val(),
      data_max: $("#data_maxima").val(),
      caixa: $("#select_caixa").val(),
      switch: true,
    };
    $.post(
      "../Models/post_receivers/select_vendas_periodo.php",
      data,
      function (ret) {
        $(".tabela_father").remove();
        $("body").append(ret);
        var dataMomentMAX = moment($("#data_maxima").val(), "YYYY-MM-DD");
        var dataMAXFormatada = dataMomentMAX.format("DD/MM/YYYY");
        var dataMomentMIN = moment($("#data_minima").val(), "YYYY-MM-DD");
        var dataMINFormatada = dataMomentMIN.format("DD/MM/YYYY");
        $(".tabela_header span").html(
          "<i class='fa-solid fa-chart-line' id='show_graphs'></i> Vendas no período de: <yellow>" +
            dataMINFormatada +
            "</yellow> até <yellow>" +
            dataMAXFormatada +
            "</yellow> <i onclick='gerarPDFFullFunction()' class='gerar_pdf fa-regular fa-file-pdf'></i>"
        );
      }
    );
  }
});
$("#input_codigo_user").on("input", function () {
  data = {
    colaborador: $(this).val(),
  };
  $.post(
    "../Models/post_receivers/select_colaborador.php",
    data,
    function (ret) {
      $("#input_nome_user").val(ret);
    }
  );
});
