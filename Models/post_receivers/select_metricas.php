
            <?php
include('../../MySql.php');
$infos = \MySql::conectar()->prepare("SELECT 
(SELECT `forma_pagamento`
FROM `tb_vendas`
WHERE date(`data`) BETWEEN '".$_POST['data_min']."' AND '".$_POST['data_max']."'
GROUP BY `forma_pagamento`
ORDER BY COUNT(*) DESC
LIMIT 1) AS formaPagamentoMaisRepetida,

(SELECT COUNT(*)
FROM `tb_vendas`
WHERE date(`data`) BETWEEN '".$_POST['data_min']."' AND '".$_POST['data_max']."') AS quantidadeVendas,
(SELECT SUM(`tb_vendas`.`valor`) FROM `tb_vendas`) AS totalValor,
(SELECT `tb_produtos`.`nome`
FROM `tb_produtos`
INNER JOIN `tb_vendas` ON `tb_vendas`.`produto` = `tb_produtos`.`id`
WHERE date(`data`) BETWEEN '".$_POST['data_min']."' AND '".$_POST['data_max']."'
GROUP BY `tb_produtos`.`id`
ORDER BY COUNT(*) DESC
LIMIT 1) AS produtoMaisVendido;
");
$infos->execute();
$infos = $infos->fetch();
print_r(json_encode($infos));

            ?>
