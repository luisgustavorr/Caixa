$('#print_fechamento').click(function(){
    data = {
       troco_inicial:$("#troco_inicial").val(),
       total_vendas:$("#total_vendas").val(),
       troco_final:$("#troco_final").val(),
       total_apurado:$("#total_apurado").val(),
       total_informado:$("#total_informado").val(),
       diferenca:$("#diferenca").val(),
       funcionario:$('#input_nome_user').val()
      };
      $.post("../Models/post_receivers/imprimir_fechamento_caixa.php", data, function (ret) {
        console.log(ret)
      });
})
function inserirValores(caixa) {
  let total_informado = 100;
  data = {
    caixa: caixa,
    colaborador : $("#input_codigo_user").val()
  };
  $.post("../Models/post_receivers/select_caixa.php", data, function (ret) {
    console.log(ret)
    let response_json = JSON.parse(ret);
    console.log(response_json.troco_inicial);
    if(response_json == {})return
    $("#troco_inicial_fechar").val(
      response_json.troco_inicial.toFixed(2).toString().replace(".", ",")
    );
    $("#total_vendas").val(
      response_json.total_valor.toFixed(2).toString().replace(".", ",")
    );
    $("#troco_final").val(
      response_json.valor_atual.toFixed(2).toString().replace(".", ",")
    );
    $("#total_apurado").val(
      (response_json.total_valor + response_json.troco_inicial)
        .toFixed(2)
        .toString()
        .replace(".", ",")
    );
   
   
    $('.valores_apurados_footer red').text("R$"+
    (response_json.total_valor + response_json.troco_inicial)
      .toFixed(2)
      .toString()
      .replace(".", ","))
  });
}
$('#mostrar').click(function (){
inserirValores($("#caixa_ser_fechado").val());

})

$('.valores_informados').keyup(function(){
    function converterParaFloat(value_input) {
        var valorFormatado = value_input

        // Remover os pontos (.) e substituir a vírgula (,) pelo ponto (.)
        var valorFloat = parseFloat(valorFormatado.replace(/\./g, '').replace(',', '.'));

        console.log('Valor em float:', valorFloat);
        return valorFloat
    }
     
    console.log()
let valor_total = parseFloat(0.00)
    $('.valores_informados').each(function(){
        if($(this).val() != ''){
            valor_total =  converterParaFloat($(this).val())+ valor_total

        }
    })
    $("#total_informado").val((valor_total.toFixed(2)).toString().replace('.',','));
    $("#diferenca").val(
        ((valor_total.toFixed(2)) - converterParaFloat($("#total_apurado").val()))
          .toFixed(2)
          .toString()
          .replace(".", ",")
      );
    $('.valores_informados_footer red').text("R$"+(valor_total.toFixed(2)).toString().replace('.',','))
})
