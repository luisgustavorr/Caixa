<?php 
include('../../MySql.php');
$caixas = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `nome` LIKE ?");
$caixas->execute(array('%'.$_POST['produto'].'%'));
$caixas = $caixas->fetchAll();
foreach ($caixas as $key => $value) {
    $value['por_peso'] == 1 ? $pesado = 'Sim' : $pesado = 'Não';
    $preco_relativo = json_decode($value["json_precos"],true);
    if($preco_relativo[$_COOKIE["caixa"]] != 0){
      $preco_relativo =$preco_relativo[$_COOKIE["caixa"]];
    }else{
      $preco_relativo =  $value["preco"];
    }
    echo '<tr class ="produto_' . $value['id'] . '" value="' . $value['id'] . '">
                 <td class="codigo_id">' . ucfirst($value['codigo_id']) . '</td>

                    <td class="nome">' . ucfirst($value['nome']) . '</td>
                    <td class="codigo">' . ucfirst($value['codigo']) . '</td>

                    <td class="preco">' . $preco_relativo . '</td>
              
                    <td ><i produto="' . $value['id'] . '" class="fa-solid editar_produto fa-pen"></i></td>


                    </tr>';
}
?>