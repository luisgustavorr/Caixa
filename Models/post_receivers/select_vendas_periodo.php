
        <?php 
        $prefix = '<div class="tabela_father">
            <div class="tabela_header">
            <i id="voltar_semana" onclick="mudarTempo(this)"class="fa-solid fa-angle-left modificadores_tempo "></i> <span>Vendas no dia: <yellow>20/07/2023</yellow> <i class="gerar_pdf fa-regular fa-file-pdf"></i></span><i id="adiantar_semana"onclick="mudarTempo(this)" class="fa-solid fa-angle-right modificadores_tempo adiantar_semana"></i>
            </div>
            <table id="table_tabela">
                <thead>
                    <tr>
                    <th>Data Venda</th>
                        <th>Valor Total</th>
                        <th>Produtos na Venda</th>
                        <th>Método de Pagamento</th>
                    </tr>
                </thead>
                <tbody>';
include('../../MySql.php');
if($_POST['caixa'] != 'todos'){
    $caixa = "AND `tb_vendas`.`caixa` = '".$_POST['caixa']."'";
}else{
    $caixa ="";
}
$row = \MySql::conectar()->prepare("SELECT `tb_vendas`.`forma_pagamento`, `tb_vendas`.`data`, SUM(`tb_vendas`.`valor`) as total_valor, GROUP_CONCAT(`tb_produtos`.`nome` SEPARATOR ', ') as nomes 
FROM `tb_vendas` 
INNER JOIN `tb_produtos` ON `tb_vendas`.`produto` = `tb_produtos`.`id` 
WHERE date(`tb_vendas`.`data`) BETWEEN ? AND ? ".$caixa."
GROUP BY `tb_vendas`.`forma_pagamento`, `tb_vendas`.`data`;");
$row->execute(array($_POST['data_min'],$_POST['data_max']));
$row = $row->fetchAll();
foreach ($row as $key => $value) {
    $data_banco = $value['data'];

    // Converte a string de data em um timestamp Unix usando a função strtotime()
    $timestamp = strtotime($data_banco);
    
    // Formata o timestamp para o formato desejado usando a função strftime()
    $data_formatada = strftime('%Hh%M-%d/%m/%Y', $timestamp);
    if(isset($_POST['switch'])){
        $prefix .="
        <tr>
        <td>$data_formatada</td>
         <td>R$".str_replace('.',',',number_format($value['total_valor'], 2))."</td>
         <td title='".$value['nomes']."'>".$value['nomes']."</td>
         <td>Dinheiro</td>
         </tr>";
    }else{
        echo "
        <tr>
        <td>$data_formatada</td>
         <td>R$".str_replace('.',',',number_format($value['total_valor'], 2))."</td>
         <td title='".$value['nomes']."'>".$value['nomes']."</td>
         <td>Dinheiro</td>
         </tr>";
    }

}
   if(isset($_POST['switch'])) {echo $prefix.'          </tr>
    </tbody>
</table>
</div>
';}
?>

  