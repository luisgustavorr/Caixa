$('.select_valor_balanca_clicker').click(function(){
    $.post("Models/post_receivers/select_valor_balanca.php",{},function(ret){
        console.log(ret)
      })
})