function atualizarSistema(){
    $.post("Models/post_receivers/atualizar_sistema.php",{},function(ret){
    console.log(ret)
    alert("Sistema atualizado!")
    location.reload()
  })
}