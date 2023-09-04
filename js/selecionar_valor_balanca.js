  $(".select_valor_balanca_clicker").click(function () {
    let attr_button_identifier = $(this).attr("button_identifier")
    $.post(
      "Models/post_receivers/select_valor_balanca.php",
      {},
      function (ret) {
        console.log(attr_button_identifier);
        if (!ret.includes("ERROR")) {
          mascaraPesoSemKeyup($("."+attr_button_identifier+"_button"), ret);
        }
      }
    );
  });
  function mascaraPesoSemKeyup(campo, valor_nao_formatado) {
    console.log(campo)
    var valor = valor_nao_formatado.replace(/[^\d]+/gi, "").reverse();
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
    console.log(resultado.reverse());
    $(campo).val(resultado.reverse())

  }
