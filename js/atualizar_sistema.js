function atualizarSistema(){
    $.post("Models/post_receivers/atualizar_sistema.php",{},function(ret){
    console.log(ret)
    alert("Sistema atualizado com sucesso para a versão 1.0.0!")
    window.location.reload(true)
  })
}