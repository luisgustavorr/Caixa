<?php 

    namespace Models;

    class LoginModel
    {
        public static function enviarFormulario(){
//             $senha = "galo1313";

// // Criar o hash da senha
// $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);
// $hashDaSenhaNoBanco = $senhaCriptografada;
@session_start();
        

if( \Painel::logado()==false){
    if(isset($_POST['logar'])){
    $user = $_POST['login'];
    $senha = $_POST['senha'];

    $logar = \MySql::conectar()->prepare('SELECT * FROM  `tb_colaboradores` WHERE `nome` = ? AND `administrador` = 1');
    $logar->execute(array($user));
    $logar = $logar->fetch();
    if($logar['codigo'] == $senha){
        echo 'AQUI';
        $_SESSION['login'] = true;
        $_SESSION['user'] = $user;
        $_SESSION['password'] = $senha;
        echo$_SESSION['login'];
        echo"<script>location.href='RelatorioABC'</script>";

    }
    }
}else{
    $_SESSION['login'] = true;
    echo"<script>location.href='RelatorioABC'</script>";
}

        }
        }
    

?>