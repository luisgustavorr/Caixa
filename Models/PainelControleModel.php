<?php 

    namespace Models;

    class PainelControleModel
    {
        public static function formarTabela(){
       
            $row = \MySql::conectar()->prepare("SELECT `tb_vendas`.`forma_pagamento`,`tb_vendas`.`data`, SUM(`tb_vendas`.`valor`) as total_valor, GROUP_CONCAT(`tb_produtos`.`nome` SEPARATOR ', ') as nomes FROM `tb_vendas` INNER JOIN `tb_produtos` ON `tb_vendas`.`produto` = `tb_produtos`.`id`  WHERE `tb_vendas`.`data` = ? GROUP BY `tb_vendas`.`data`");
            $row->execute(array(date('Y-m-d H:i:s')));
            $row = $row->fetchAll();
         
            foreach ($row as $key => $value) {
                $data_banco = $value['data'];

                // Converte a string de data em um timestamp Unix usando a função strtotime()
                $timestamp = strtotime($data_banco);
                
                // Formata o timestamp para o formato desejado usando a função strftime()
                $data_formatada = strftime('%Hh%M-%d/%m/%Y', $timestamp);
               echo "
               <tr>
               <td> $data_formatada </td>
                <td> R$".str_replace('.',',',number_format($value['total_valor'], 2))." </td>
                <td title='".$value['nomes']."'> ".$value['nomes']." </td>
                <td> Dinheiro </td>
                
                </tr>   ";
            }
        }
        public static function buscarDados($request){
            $infos = \MySql::conectar()->prepare("SELECT 
            (SELECT `forma_pagamento`
             FROM `tb_vendas`
             GROUP BY `forma_pagamento`
             ORDER BY COUNT(*) DESC
             LIMIT 1) AS formaPagamentoMaisRepetida,
             
            (SELECT COUNT(*)
             FROM `tb_vendas`) AS quantidadeVendas,
             (SELECT SUM(`tb_vendas`.`valor`) FROM `tb_vendas`) AS totalValor,
            (SELECT `tb_produtos`.`nome`
             FROM `tb_produtos`
             INNER JOIN `tb_vendas` ON `tb_vendas`.`produto` = `tb_produtos`.`id`
             GROUP BY `tb_produtos`.`id`
             ORDER BY COUNT(*) DESC
             LIMIT 1) AS produtoMaisVendido");
            $infos->execute();
            $infos = $infos->fetch();
            if($request == 'totalValor'){
                print_r(number_format($infos[$request],2,',','.'));

            }else{
                print_r($infos[$request]);

            }
        }

        }
