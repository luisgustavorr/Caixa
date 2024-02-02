<?php 
require ('../../MySql.php');
if(isset($_POST['no_permission'])){
    $infos = \MySql::conectar()->prepare("SELECT * FROM `tb_caixas` WHERE caixa = ? ");
$infos->execute(array($_POST['caixa']));
$infos = $infos->fetch();
if(empty($infos)){
    echo "Caixa não encontrado";
}else{
    $expira = time() + (30 * 24 * 60 * 60);
    $valorCookie = $_POST['caixa'];
    
    // Definir o cookie com SameSite=Lax
    setcookie("caixa", $valorCookie, $expira, "/", "", false, true);

// Exemplo de uso: ler o valor do cookie
if (isset($_COOKIE["caixa"])) {
    $valor = $_COOKIE["caixa"];
    echo "Valor do cookie: " . $valor;
} else {
    echo "Cookie não encontrado.";
}
}
}else{
    $infos = \MySql::conectar()->prepare("SELECT tb_caixas.*, SUM(tb_vendas.valor) AS total_valor FROM tb_caixas INNER JOIN tb_vendas ON tb_caixas.caixa = tb_vendas.caixa WHERE tb_caixas.caixa = ? AND tb_vendas.colaborador = ? GROUP BY tb_caixas.id; ");
$infos->execute(array($_POST['caixa'],$_POST['colaborador']));
$infos = $infos->fetch();
print_r(json_encode($infos));
}

?>