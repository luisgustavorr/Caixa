let timeoutId;
let input_codigo_focado = false;
let condicao_favoravel = true;
console.log('atualização aplicada')
$(".colab_code").keyup(function () {
  if ($(this).val() == "") {
    $(this).css("animation", "pulse 3s infinite");
  }
});
if ($(".colab_code").val() == "") {
  $(this).css("animation", "pulse 3s infinite");
}
$('#teste_impressora').click(function(){
  $.post("Models/post_receivers/teste_impressora.php", data, function (ret) {
    alert(ret)
  })
})
function getCookie(name) {
  let cookie = {};

  document.cookie.split(";").forEach(function (el) {
    let [k, v] = el.split("=");
    cookie[k.trim()] = v;
  });

  return cookie[name];
}

let caixa = getCookie("last_codigo_colaborador");
function setCaixa(code, callback) {
 
  var data = {
    colaborador: code,
    blue_sky: true,
  };
  console.log(data);

  $.post("Models/post_receivers/select_colaborador.php", data, function (ret) {
    console.log('ret'+ret);
    // Chama a função de retorno de chamada e passa o valor retornado
    callback(ret);
  });
}
setCaixa(caixa, function (caixa_retornado) {
  console.log(caixa_retornado);
  caixa = caixa_retornado;
  $("#blocked_fazer_sangria").attr("id", "fazer_sangria");
  verificarValorCaixa(getCookie("last_codigo_colaborador"));
});
console.log(caixa);
let produto_object = {};
$("#notification i").click(function () {
  $("#notification").css("display", "none");
});

$(".valores_informados").keyup(function () {
  soma = 0;
  $(".valores_informados").each(function (index) {
    if( $(this).val() != ''){
          if ($(this).attr("id") == "sangria_informadas") {
      soma -= parseFloat($(this).val().replace(".", "").replace(",", "."));
    } else {
      soma += parseFloat($(this).val().replace(".", "").replace(",", "."));
    }

}

    console.log($(this).val().replace(".", "").replace(",", "."));
  });

  $(".valores_informados_footer red").text(
    "R$" + soma.toFixed(2).replace(".", ",")
  );
});
$(".modal_fechar_caixa").submit(function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  $.ajax({
    type: "POST",
    url: "Models/post_receivers/insert_fechamento.php",
    data: formData,
    processData: false,
    contentType: false,
    success: function (data) {
      console.log(data);
      if(data != ''){
    
      }else{
        location.reload()
      }
    },
  });


});
$("#abrir_lista_pedidos").click(function () {
  if (
    $("#abrir_lista_pedidos i").attr("class").includes("fa-solid fa-chevron-up")
  ) {
    $(".lista_pedidos").animate({ height: "0" }, function () {
      $(".lista_pedidos").css("display", "none");
    });

    $("#abrir_lista_pedidos i").attr("class", "fa-solid fa-chevron-down");
  } else {
    $(".lista_pedidos").animate({ height: "200px" });
    $(".lista_pedidos").css("display", "flex");

    $("#abrir_lista_pedidos i").attr("class", "fa-solid fa-chevron-up");
  }
});
$(".modal_anotar_pedido tbody").children().remove();

$(".tags_produto_name").keyup(function (e) {
  let produto = $(this).val().replace(".", "");
  if (e.keyCode == 190) {
    $(".modal_anotar_pedido tbody").append(
      '<tr preco_produto="" produto="' +
        produto.replace(" ", "_") +
        '" quantidade="' +
        $("#quantidade_produto_pedido").val() +
        '" class="produto_pedido' +
        produto.replace(" ", "_") +
        '"><td>' +
        $("#quantidade_produto_pedido").val() +
        "</td><td>" +
        produto +
        "</td><td><input type='text'class='oders_inputs input_valor_pedido_produto' produto='" +
        produto.replace(" ", "_") +
        "' onKeyUp='mascaraMoeda(this, event)' id='preco_produto_" +
        produto.replace(" ", "_") +
        "'></td><td id='valor_produto_total_" +
        produto.replace(" ", "_") +
        "' >" +
        100 +
        '</td> <td produto="' +
        produto.replace(" ", "_") +
        '" class="remove_item_pedido ">-</td>'
    );
    $(".tags_produto_name").val("");
    $("#quantidade_produto_pedido").val("1");
    $(".remove_item_pedido").click(function () {
      $(".produto_pedido" + $(this).attr("produto")).remove();
    });
    verificarCondicoes();
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
  }
});
$("#desc_produto").on("keyup", function () {
  if ($(this).val() == "") {
    $(".search_results").css("display", "none");
  } else {
    $(".search_results").css("display", "block");
    data = {
      pesquisa: $(this).val(),
    };

    $.post("Models/post_receivers/select_pesquisa.php", data, function (ret) {
      try {
        // Certifique-se de que a resposta seja válida JSON antes de fazer o parse
        const row = JSON.parse(ret);
        
        $(".search_results").empty();
        row.forEach((element) => {
          $(".search_results").append(
            '<span produto="' +
              element.codigo +
              '" class="resultado_pesquisa">' +
              element.nome +
              "</span>"
          );
        });
        console.log($(".resultado_pesquisa"))
        $(".resultado_pesquisa").click(function () {
          data = {
            barcode: $(this).attr("produto"),
          };

          $.post("Models/post_receivers/select_produto.php", data, function (ret) {
            try {
              // Certifique-se de que a resposta seja válida JSON antes de fazer o parse
              produto_object = JSON.parse(ret);
              
              console.log(produto_object);
              $("#desc_produto").val(produto_object.nome);
            } catch (error) {
              console.error("Erro ao analisar JSON da resposta select_produto.php:", error);
            }
          });
        });
      } catch (error) {
        console.error("Erro ao analisar JSON da resposta select_pesquisa.php:", error);
      }
    });
  }
});

$("#add_produto").click(function () {
  console.log(produto_object)
  pesquisarProdutoPorCodigoDeBarras(produto_object);
});
let executando = false;
$("#codigo_produto").on("keyup", function (e) {
  if (executando) return;
  executando = true;
  if ($(this).val() === "" && e.keyCode == "Backspace") {
    $(".search_results_by_barcode").css("display", "none");
  } else {
    $(".search_results_by_barcode").css("display", "block");
    data = {
      pesquisa: $(this).val(),
      codigo: true,
    };

    $.post("Models/post_receivers/select_pesquisa.php", data, function (ret) {
      row = JSON.parse(ret);

      if (row.length == 0) {
        $(".search_results_by_barcode").css("display", "none");
      }
      $(".search_results_by_barcode").empty();
      row.forEach((element) => {
        $(".search_results_by_barcode").append(
          '<span produto="' +
            element.codigo +
            '" class="resultado_pesquisa_by_barcode">' +
            element.nome +
            "</span>"
        );
      });
      $(".resultado_pesquisa_by_barcode").click(function () {
        console.log("aqui")
        data = {
          barcode: $(this).attr("produto"),
        };

        $.post(
          "Models/post_receivers/select_produto.php",
          data,
          function (ret) {
            produto_object = ret;
            $(".search_results_by_barcode").css("display", "none");
            let row = JSON.parse(ret);
            console.log(row.nome);
            $("#desc_produto").val(row.nome);
          }
        );
      });
    });
  }
  executando = false;
});
function pesquisarProdutoPorCodigoDeBarras(ret) {
  let total_valor = 0;
  console.log(ret);
  darker ? (darker_class = "darker") : (darker_class = "");
  if(typeof ret != "object"){
    row = JSON.parse(ret);

  }else{
    row = ret

  }
  quantidade = 0;

  if (
    parseFloat(
      $(".row_id_" + row.id)
        .find(".preco_produto")
        .attr("quantidade_produto")
    ) > 0
  ) {
    quantidade =
      parseFloat($("#quantidade_produto").val()) +
      parseFloat(
        $(".row_id_" + row.id)
          .find(".preco_produto")
          .attr("quantidade_produto")
      );
  } else {
    quantidade = parseFloat($("#quantidade_produto").val());
  }
  $(".row_id_" + row.id).remove();

  if (typeof row === "boolean") return;
  $("#desc_produto").val(row.nome);

  $("#tabela_produtos tbody").append(
    "   <tr class='row_id_" +
      row.id +
      " " +
      darker_class +
      " '><td>" +
      row.id +
      "</td><td>" +
      row.codigo +
      "</td><td>" +
      row.nome +
      "</td><td " +
      row.id +
      " quantidade_produto = '" +
      quantidade +
      "' id_produto='" +
      row.id +
      "' nome_produto='" +
      row.nome +
      "' class='preco_produto'>" +
      row.preco +
      "</td><td >" +
      quantidade +
      "</td></tr>"
  );
  $("#tabela_produtos .preco_produto").each(function () {
    console.log("valor antigo :", parseFloat(total_valor));
    console.log("quant :", $(this).attr("quantidade_produto"));
    console.log("id :", $(this).attr("id_produto"));
    total_valor =
      parseFloat(total_valor) +
      parseFloat($(this).text().replace(",", ".")) *
        $(this).attr("quantidade_produto");
    total_valor = isNaN(total_valor) ? 0 : total_valor;
    console.log(total_valor);
    console.log("-------------------------");

    $(".tiny_row_id" + $(this).attr("id_produto")).remove();
    $(".venda_preview_body tbody").append(
      '<tr class="tiny_row_id' +
        $(this).attr("id_produto") +
        '"><td>' +
        $(this).attr("nome_produto") +
        "</td><td class='quantidade_produto' preco_produto='" +
        parseFloat($(this).text().toString().replace(",", ".")) +
        "' id_produto='" +
        $(this).attr("id_produto") +
        "'>" +
        $(this).attr("quantidade_produto") +
        "x</td><td>R$" +
        $(this).text() +
        "</td><td>R$" +
        parseFloat(
          (
            parseFloat($(this).text().toString().replace(",", ".")) *
            parseFloat($(this).attr("quantidade_produto"))
          ).toFixed(2)
        ) +
        '</td><td><i class="fa-regular fa-trash-can trash_inactive remove_item" row="tiny_row_id' +
        $(this).attr("id_produto") +
        '" ></i></td></tr>'
    );
  });
  $(".valor_total strong").text(
    "R$:" + total_valor.toFixed(2).toString().replace(".", ",")
  );
  $(".venda_preview_bottom").text(
    "Subtotal: R$:" + total_valor.toFixed(2).toString().replace(".", ",")
  );
  $("#valor_compra").text(
    "R$" + total_valor.toFixed(2).toString().replace(".", ",")
  );
  $(".remove_item").click(function () {
    if ($(this).attr("class").includes("trash_inactive")) {
      $(this).addClass("trash_activated");
      $(this).removeClass("trash_inactive");
    } else {
      $(this).removeClass("trash_activated");
      $(this).addClass("trash_inactive");
    }
  });

  darker = !darker;
  $("#codigo_produto").val("");
  $("#desc_produto").val("");
  $("#quantidade_produto").val(1);

  $(".search_results").css("display", "none");
  $(".search_results_by_barcode").css("display", "none");
}
let side_bar_aberta = false;
$(".menu").click(function () {
  if (side_bar_aberta) {
    $("#sidebar").animate({ width: "0" });
    $("#sidebar span").css("display", "none");
    $("#salvar_caixa").css("display", "none");
  } else {
    $("#sidebar").animate({ width: "300px" }, 200, function () {
      $("#sidebar .princip_span").css("display", "block");
      $("#salvar_caixa").css("display", "flex");
    });
  }
  side_bar_aberta = !side_bar_aberta;
});

function atualizarHorario() {
  moment.locale("pt-br");
  var dataAtual = moment().format("ddd: DD/MM/YYYY HH[h]mm");
  $(".horario_atual_finder").text(dataAtual);
}

function verificarValorCaixa(codigoColab) {
  moment.locale("en");
  const dataAtual = moment();
  const dataFutura = dataAtual.add(30, "days");
  const GMTstring = dataFutura.utc().format("ddd, DD MMM YYYY HH:mm:ss [GMT]");

  document.cookie =
    "last_codigo_colaborador=" + codigoColab + ";SameSite=Strict";
  let dataMoment = moment();
  var dataNovaAdiantada = dataMoment.add(30, "days");

  data = {
    caixa: caixa,
  };

  $.post("Models/post_receivers/select_valor_caixa.php", data, function (ret) {
    let valor = ret == "" ? (valor = 0) : parseFloat(ret);

    if (valor >= 150) {
      $("#fazer_sangria").css("animation", "hysterical_pulse 0.7s infinite");
    } else if (valor >= 200) {
      $("#fazer_sangria").css("animation", "pulse 3s infinite");
    }
  });
}
verificarValorCaixa(getCookie("last_codigo_colaborador"));
function valorCaixa() {
  data = {
    caixa: caixa,
  };
  console.log(caixa);
  $.post("Models/post_receivers/select_valor_caixa.php", data, function (ret) {
    console.log(ret);
    let valor = ret == "" ? (valor = 0) : parseFloat(ret);
    $("#valor_sangria").val(valor.toFixed(2).replace(".", ","));
    $(".valor_caixa_father red").text(
      "R$" + valor.toFixed(2).replace(".", ",")
    );
  });
}
atualizarHorario();
setInterval(function () {
  atualizarHorario();
}, 10000);

$(".modal_sangria").submit(function (e) {
  e.preventDefault();
  setCaixa($("#colaborador_input").val(), function (caixa_retornado) {
    console.log(caixa_retornado);
    caixa = caixa_retornado;
  });
  data = {
    path: $("#include_path").val(),
    caixa: caixa,
    valor: $(".valor_caixa_apos_father red")
      .text()
      .replace("R$", "")
      .replace(",", "."),
    valor_sangria: $("#valor_sangria").val().replace(",", "."),
    mensagem: $("#motivo_sangria").val(),
    colaborador: $("#colaborador_input").val(),
  };

  $.post("Models/post_receivers/insert_sangria.php", data, function (ret) {
    let vazio = ret;
    if (!vazio) {
      location.reload();
    } else {
      console.log(ret)
      alert(ret);
    }
  });
});
$("#valor_sangria").keyup(function () {
  //valor caixa - valor retirado
  let valor_caixa = $(".valor_caixa_father red")
    .text()
    .replace("R$", "")
    .replace(",", ".");
  let valor_retirado = $(this).val().replace("R$", "").replace(",", ".");
  if (parseFloat(valor_caixa) > parseFloat(valor_retirado)) {
    $(".valor_caixa_apos_father red").text(
      "R$" +
        Math.abs(parseFloat(valor_caixa) - parseFloat(valor_retirado))
          .toFixed(2)
          .toString()
          .replace(".", ",")
    );
  } else {
    $(".valor_caixa_apos_father red").text("R$00,00");
  }
});
$("#whatsapp_cliente").mask("(00) 0 0000-0000");
function verificarCondicoes() {
  $("#codigo_produto").focus(function () {
    input_codigo_focado = true;
  });
  $("#codigo_produto").blur(function () {
    input_codigo_focado = false;
  });
  $(".oders_inputs").focus(function () {
    condicao_favoravel = false;
  });
  $(".oders_inputs").blur(function () {
    condicao_favoravel = true;
  });
}
verificarCondicoes();
$(".pagamento_input").change(function () {
  if (
    $(this).val() == "Cartão Crédito" ||
    $(this).attr("id") == "quantidade_parcelas" ||
    $(this).val() == "Parcelado"
  ) {
    $(".a_vista").css("display", "none");
    $(".parcelado").css("display", "block");
    $("#tipo_pagamento").val("Parcelado");
  } else {
    $("#tipo_pagamento").val("À Vista");
    $("#quantidade_parcelas").val("1x");
    $(".parcelado").css("display", "none");
    $(".a_vista").css("display", "block");
  }
  $("#" + $(this).attr("id") + "_text").text($(this).val());
});
$("fundo").click(function () {
  $('#editando').val(false)
  $(".modal").each(function () {
    $(this).css("display", "none");
    $("fundo").css("display", "none");
  });
});
function abrirModal(modal) {
  $("." + modal).css("display", "flex");
  $("fundo").css("display", "flex");
  if (modal == "modal_sangria") {
    valorCaixa();
  }
}
$("#valor_recebido_input").keyup(function () {
  console.log("aqui");
  let valor_calculado = parseFloat(
    Math.abs(
      parseFloat(
        $("#valor_total_input").val().replace(".", "").replace(",", ".")
      ) - parseFloat($(this).val().replace(",", "."))
    ).toFixed(2)
  )
    .toString()
    .replace(".", ",");
  if (
    parseFloat($("#valor_total_input").val().replace(",", ".")) >
    parseFloat($(this).val().replace(",", "."))
  ) {
    $("#valor_calculado_input").val("");
  } else {
    $("#valor_calculado_input").val(valor_calculado);
  }
});
$(".finalizar_venda").click(function () {
  let valor_compra = parseFloat(
    $("#valor_compra").text().replace("R$", "").replace(",", ".")
  );
  let produtos = [];
  $(".venda_preview_body .quantidade_produto").each(function (index) {
    let produto_info = {
      id: $(this).attr("id_produto"),
      quantidade: $(this).text().replace("x", ""),
      preco: $(this).attr("preco_produto"),
    };
    produtos[index] = produto_info;
  });
  let caixa_para_venda =''

  setCaixa($("#codigo_colaborador_venda").val(), function (caixa_retornado) {
    console.log('certo:'+caixa_retornado);
    caixa = caixa_retornado;
    $("#blocked_fazer_sangria").attr("id", "fazer_sangria");
    verificarValorCaixa(getCookie("last_codigo_colaborador"));
  });
  data = {
    colaborador: $("#codigo_colaborador_venda").val(),
    valor: valor_compra,
    produtos: produtos,
    pagamento: $("#metodo_pagamento_princip").val(),
  };

  $.post("Models/post_receivers/insert_venda.php", data, function (ret) {

    verificarValorCaixa();
    if (ret != "") {
      alert("Código de usuario inválido");
    } else {
      location.reload();
    }
  });
});
$(".finalizar_venda_button").click(function () {
  $("#valor_total_input").val($("#valor_compra").text().replace("R$", ""));
  if (
    $("#metodo_pagamento_princip").val() == "Dinheiro" &&
    $(this).attr("first") == "sim"
  ) {
    $("#valor_total_input").val($("#valor_compra").text().replace("R$", ""));
    $(".modal_troco").css("display", "block");
    $(".modal_pagamento").css("display", "none");
  } else {
    let valor_compra = parseFloat(
      $("#valor_compra").text().replace("R$", "").replace(",", ".")
    );
    let produtos = [];
    $(".venda_preview_body .quantidade_produto").each(function (index) {
      let produto_info = {
        id: $(this).attr("id_produto"),
        quantidade: $(this).text().replace("x", ""),
        preco: $(this).attr("preco_produto"),
      };
      produtos[index] = produto_info;
    });
    setCaixa($("#codigo_colaborador_venda").val(), function (caixa_retornado) {
      console.log('certo:'+caixa_retornado);
      caixa = caixa_retornado;
      $("#blocked_fazer_sangria").attr("id", "fazer_sangria");
      verificarValorCaixa(getCookie("last_codigo_colaborador"));
    });
    data = {
      colaborador: $("#codigo_colaborador_venda").val(),
      valor: valor_compra,
      produtos: produtos,
      pagamento: $("#metodo_pagamento_princip").val(),
    };

    $.post("Models/post_receivers/insert_venda.php", data, function (ret) {
      console.log(ret)
      if (ret != "") {
        alert("Código de usuario inválido");

      } else {
        moment.locale("en");
        const dataAtual = moment();
        const dataFutura = dataAtual.clone().add(30, "days"); // Usamos 'clone()' para não modificar a data atual
        const GMTstring = dataFutura.utc().format("ddd, DD MMM YYYY HH:mm:ss [GMT]");
        
        // Define o cookie com a data de expiração
        document.cookie = `last_codigo_colaborador=${$("#codigo_colaborador_venda").val()}; SameSite=Strict; expires=${GMTstring}`;
        
        let dataMoment = moment();
        var dataNovaAdiantada = dataMoment.add(30, "days");
        
        location.reload();
      }
    });
  }
});
$(document).keyup(function (event) {
  if (
    event.code.includes("Digit") &&
    condicao_favoravel &&
    window.location.href.includes("http://localhost/MixSalgados/Caixa/")
  ) {
    var key = event.keyCode || event.which;
    key = String.fromCharCode(key);

    if (input_codigo_focado == false) {
      $("#codigo_produto").val($("#codigo_produto").val() + key);
    }
    const barcode = $("#codigo_produto").val().trim();

    clearTimeout(timeoutId);

    pesquisarProduto(barcode);
  } else if (event.code == "Delete") {
    $(".trash_activated").each(function () {
      $(".valor_total strong").text(
        "R$:" +
          (
            parseFloat(
              $(".valor_total strong")
                .text()
                .replace("R$:", "")
                .replace(",", ".")
            ).toFixed(2) -
            parseFloat(
              $(this)
                .parent()
                .parent()
                .find(".quantidade_produto")
                .attr("preco_produto")
            ).toFixed(2) *
              $(this)
                .parent()
                .parent()
                .find(".quantidade_produto")
                .text()
                .replace("x", "")
          )
            .toFixed(2)
            .toString()
            .replace(".", ",")
      );
      $("#valor_compra").text($(".valor_total strong").text());
      $(".venda_preview_bottom").text($(".valor_total strong").text());
      $("." + $(this).attr("row")).remove();
      $(
        "." + $(this).attr("row").replace("tiny_", "").replace("id", "id_")
      ).remove();
    });
  }
});
let darker = false;

function pesquisarProduto(barcode) {
  if (barcode.length == 13 || barcode.length == 8) {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(function () {
      var data = {
        barcode: barcode,
      };
      var startTime = performance.now();
      $.post("Models/post_receivers/select_produto.php", data, function (ret) {
        var endTime = performance.now();
        var row = JSON.parse(ret);
        $("#desc_produto").val(row.nome);
        var duration = endTime - startTime;
        console.log("Requisição concluída em " + duration + "ms");
      });
      $("#codigo_produto").val("");
    }, 350);
  }
}


//Mascara de moeda
String.prototype.reverse = function () {
  return this.split("").reverse().join("");
};

function mascaraMoeda(campo, evento) {
  var tecla = !evento ? window.event.keyCode : evento.which;
  var valor = campo.value.replace(/[^\d]+/gi, "").reverse();
  var resultado = "";
  var mascara = "##.###.###,##".reverse();
  for (var x = 0, y = 0; x < mascara.length && y < valor.length; ) {
    if (mascara.charAt(x) != "#") {
      resultado += mascara.charAt(x);
      x++;
    } else {
      resultado += valor.charAt(y);
      y++;
      x++;
    }
  }
  campo.value = resultado.reverse();
}
function mascaraPeso(campo, evento) {
  var tecla = !evento ? window.event.keyCode : evento.which;
  var valor = campo.value.replace(/[^\d]+/gi, "").reverse();
  var resultado = "";
  var mascara = "##.###".reverse();
  for (var x = 0, y = 0; x < mascara.length && y < valor.length; ) {
    if (mascara.charAt(x) != "#") {
      resultado += mascara.charAt(x);
      x++;
    } else {
      resultado += valor.charAt(y);
      y++;
      x++;
    }
  }
  campo.value = resultado.reverse();
}
