let timeoutId;
let input_codigo_focado = false;
let condicao_favoravel = true;
let caixa = $('#caixa_selecionado').val()
$('#abrir_lista_pedidos').click(function(){
  if($('#abrir_lista_pedidos i').attr("class").includes('fa-solid fa-chevron-up')){
  $(".lista_pedidos").animate({'height':'0'},function(){
    $(".lista_pedidos").css('display','none')

  })

  $('#abrir_lista_pedidos i').attr("class",'fa-solid fa-chevron-down')
  }else{
    $(".lista_pedidos").animate({'height':'200px'})
  $(".lista_pedidos").css('display','block')

    $('#abrir_lista_pedidos i').attr("class",'fa-solid fa-chevron-up')

  }
})
$(".modal_anotar_pedido tbody").children().remove();

$(".tags_produto_name").keyup(function () {
  console.log("a");
  data = {
    pesquisa: $(this).val(),
  };
  let pesquisa_bar = [];

  $.post("Models/post_receivers/select_pesquisa.php", data, function (ret) {
    row = JSON.parse(ret);
    row.forEach((element) => {
      pesquisa_bar.push(element.id + "-" + element.nome);
    });
  });
  $(".tags_produto_name").autocomplete({
    source: pesquisa_bar,
    select: function (event, ui) {
      setTimeout(() => {
        let produto = $(".tags_produto_name").val().split("-");
        $(".produto_pedido" + produto[0]).remove();

        let produtos_info = row.filter(
          (Element) => Element.id == parseInt(produto[0])
        );
        $(".modal_anotar_pedido tbody").append(
          '<tr preco_produto="' +
            produtos_info[0].preco.toString().replace(",", ".") +
            '" produto="' +
            produto[0] +
            '" quantidade="' +
            $("#quantidade_produto_pedido").val() +
            '" class="produto_pedido' +
            produto[0] +
            '"><td>' +
            $("#quantidade_produto_pedido").val() +
            "</td><td>" +
            produto[1] +
            "</td><td>" +
            produtos_info[0].preco +
            "</td><td >" +
            parseFloat(
              parseFloat(produtos_info[0].preco.replace(",", ".")).toFixed(2) *
                parseInt($("#quantidade_produto_pedido").val())
            ).toFixed(2) +
            '</td> <td produto="' +
            produto[0] +
            '" class="remove_item_pedido ">-</td>'
        );
        console.log();
        $(".tags_produto_name").val("");
        $("#quantidade_produto_pedido").val("1");
        $(".remove_item_pedido").click(function () {
          $(".produto_pedido" + $(this).attr("produto")).remove();
        });
      }, 100);
    },
  });
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
      row = JSON.parse(ret);
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
      $(".resultado_pesquisa").click(function () {
        data = {
          barcode: $(this).attr("produto"),
        };

        $.post(
          "Models/post_receivers/select_produto.php",
          data,
          function (ret) {
            pesquisarProdutoPorCodigoDeBarras(ret);
            $(".search_results").css("display", "none");
            $("#desc_produto").val("");
          }
        );
      });
    });
  }
});
$("#codigo_produto").on("keyup", function () {
  if ($(this).val() === "") {
    console.log("a");
    $(".search_results_by_barcode").css("display", "none");
  } else {
    $(".search_results_by_barcode").css("display", "block");
    data = {
      pesquisa: $(this).val(),
      codigo: true,
    };
    $.post("Models/post_receivers/select_pesquisa.php", data, function (ret) {
      row = JSON.parse(ret)
      console.log(row)

      if(row.length ==0){
        $(".search_results_by_barcode").css("display", "none");
      }
      row.forEach((element) => {
      $(".search_results_by_barcode").empty();
        

        $(".search_results_by_barcode").append(
          '<span produto="' +
            element.codigo +
            '" class="resultado_pesquisa_by_barcode">' +
            element.nome +
            "</span>"
        );
      });
      $(".resultado_pesquisa_by_barcode").click(function () {
        data = {
          barcode: $(this).attr("produto"),
        };

        $.post(
          "Models/post_receivers/select_produto.php",
          data,
          function (ret) {
            pesquisarProdutoPorCodigoDeBarras(ret);
            $(".search_results_by_barcode").css("display", "none");
            $("#desc_produto").val("");
          }
        );
      });
    });
  }
});

function pesquisarProdutoPorCodigoDeBarras(ret) {
  let total_valor = 0;
  darker ? (darker_class = "darker") : (darker_class = "");
  row = JSON.parse(ret);
  quantidade = 0
  
  if(parseInt($(".row_id_"+row.id).find('.preco_produto').attr("quantidade_produto")) > 0){
     quantidade = parseInt($("#quantidade_produto").val()) + parseInt($(".row_id_"+row.id).find('.preco_produto').attr("quantidade_produto")) 
  }else{
     quantidade = parseInt($("#quantidade_produto").val())
  }
  $(".row_id_"+row.id).remove()

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
      "</td><td>170.99.00</td><td>" +
      row.nome +
      "</td><td quantidade_produto = '"+
      quantidade
      +"' id_produto='" +
      row.id +
      "' nome_produto='" +
      row.nome +
      "' class='preco_produto'>" +
      row.preco +
      "</td><td >" +
      quantidade +
      "</td><td>0</td><td>A P</td><td>0,00</td></tr>"
  );
  $("#tabela_produtos .preco_produto").each(function () {
    
    total_valor =parseFloat(total_valor) + parseFloat($(this).text().replace(",", ".")) * quantidade;
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
        "'>" + $(this).attr("quantidade_produto")
        +
        "x</td><td>R$" +
        $(this).text() +
        "</td><td>R$" +
        parseFloat(
          (
            parseFloat($(this).text().toString().replace(",", ".")) *
            parseInt($(this).attr("quantidade_produto"))
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
  $("#codigo_produto").val('')
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
    $("#sidebar").animate({ width: "300px" },200, function () {
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

function verificarValorCaixa() {
  data = {
    caixa: caixa,
  };

  $.post("Models/post_receivers/select_valor_caixa.php", data, function (ret) {
    let valor = ret == "" ? (valor = 0) : parseFloat(ret);
    if (valor >= 30) {
      $("#fazer_sangria").css("animation", "hysterical_pulse 0.7s infinite");
    } else if (valor >= 20) {
      $("#fazer_sangria").css("animation", "pulse 3s infinite");
    }
  });
}
verificarValorCaixa();
function valorCaixa() {
  data = {
    caixa: caixa,
  };

  $.post("Models/post_receivers/select_valor_caixa.php", data, function (ret) {
    let valor = ret == "" ? (valor = 0) : parseFloat(ret);
    console.log(ret);
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
  data = {
    path: $('#include_path').val(),
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
    console.log(ret);
    let vazio = ret;
    if (!vazio) {
      location.reload();
    } else {
      alert("Código de usuário inválido");
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
  let valor_calculado = parseFloat(
    Math.abs(
      parseFloat($("#valor_total_input").val().replace(",", ".")) -
        parseFloat($(this).val().replace(",", "."))
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

$("#finalizar_venda_modal_button").click(function () {
  //$('#valor_compra').text().replace('R$','') == valor sem R$
  $("#valor_total_input").val($("#valor_compra").text().replace("R$", ""));
  if ($("#metodo_pagamento").val() == "Dinheiro") {
    $("#valor_total_input").val($("#valor_compra").text().replace("R$", ""));
    $(".modal_troco").css("display", "block");
    $(".modal_pagamento").css("display", "none");
  } else {
    let valor_compra = parseFloat(
      $("#valor_compra").text().replace("R$", "").replace(",", ".")
    );
    let produtos = [];
    $(".venda_preview_body .quantidade_produto").each(function (index) {
      console.log($(this).attr("id_produto") + $(this).text().replace("x", ""));
      let produto_info = {
        id: $(this).attr("id_produto"),
        quantidade: $(this).text().replace("x", ""),
        preco: $(this).attr("preco_produto"),
      };
      produtos[index] = produto_info;
    });
    data = {
      colaborador:$('#codigo_colaborador_venda').val(),
      valor: valor_compra,
      caixa: caixa,
      produtos: produtos,
      pagamento: $("#metodo_pagamento").val(),
    };

    $.post("Models/post_receivers/insert_venda.php", data, function (ret) {
      console.log(ret)
    });
  }
});
$(document).keyup(function (event) {
  if (event.code.includes("Digit") && condicao_favoravel && window.location.href.includes('http://localhost/MixSalgados/Caixa/' )) {
    var key = event.keyCode || event.which;
    key = String.fromCharCode(key);
    console.log(event.code);

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
  timeoutId = setTimeout(function () {
    if (barcode.length == 13 || barcode.length == 8) {
      data = {
        barcode: barcode,
      };

      $.post("Models/post_receivers/select_produto.php", data, function (ret) {
        pesquisarProdutoPorCodigoDeBarras(ret);
      });
      console.log("Código de barras lido:", barcode);
      $("#codigo_produto").val("");
      $("#desc_produto").val("");
    }
  }, 350);
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
