
            <?php
            if($_POST['caixa'] != 'todos'){
                $caixa = "AND `tb_vendas`.`caixa` = '".$_POST['caixa']."'";
            }else{
                $caixa ="";
            }
include('../../MySql.php');
            $prefix = '<div class="tabela_father">
            <div class="tabela_header">
            <i id="voltar_semana" onclick="mudarTempo(this)"  class="fa-solid fa-angle-left modificadores_tempo "></i><span>Vendas no dia: <yellow>20/07/2023</yellow> <i   class="gerar_pdf fa-regular fa-file-pdf"></i></span><i id="adiantar_semana"onclick="mudarTempo(this)"  class="fa-solid fa-angle-right modificadores_tempo adiantar_semana"></i>
            </div>
            <table id="table_tabela">
                <thead>
                    <tr>
                        <th>Podium</th>
                        <th>Produto</th>
                        <th>Quantidade de Vendas</th>
                        <th>Valor Uni</th>
                        <th>Valor total de Vendas</th>
                    </tr>
                </thead>
                <tbody>';
            $row = \MySql::conectar()->prepare("SELECT p.nome, p.preco, vendas_contadas.ordem, vendas_contadas.total_vendas
            FROM tb_produtos p
            INNER JOIN (
                SELECT produto, total_vendas,
                       @row_number := @row_number + 1 AS ordem
                FROM (
                    SELECT produto, COUNT(*) as total_vendas
                    FROM tb_vendas
                    WHERE date(data) BETWEEN ? AND ? ".$caixa."
                    GROUP BY produto
                    ORDER BY COUNT(*) DESC
                ) vendas_contadas, (SELECT @row_number := 0) AS rn_init
            ) vendas_contadas ON p.id = vendas_contadas.produto
            ORDER BY vendas_contadas.total_vendas DESC");
            $row->execute(array($_POST['data_min'],$_POST['data_max']));
            $row = $row->fetchAll();
            $ordem = 1;
            foreach ($row as $key => $value) {
            $trocando_virgula = str_replace(',','.',$value['preco']);

                if(isset($_POST['switch'])){
                    $prefix .="
                    <tr>
                    <td>".$ordem."#</td>
                     <td>".$value['nome']."</td>
                     <td >".$value['total_vendas']."</td>
                     <td>R$".$value['preco']."</td>
                     <td>R$".str_replace('.',',',number_format($trocando_virgula *$value['total_vendas'], 2)) ."</td>

                     </tr>";
                }else{
                    echo "
                    <tr>
                    <td>".$ordem."#</td>
                     <td>".$value['nome']."</td>
                     <td >".$value['total_vendas']."</td>
                     <td>R$".$value['preco']."</td>
                     <td>R$".str_replace('.',',',number_format($trocando_virgula *$value['total_vendas'], 2)) ."</td>

                     </tr>";
                }
                  
                $ordem +=1;
            
            }
            if(isset($_POST['switch'])){
                echo $prefix."            </tr>
                </tbody>
            </table>
        </div>";
            }
            ?>
