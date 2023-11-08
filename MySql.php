<?php 
function verificaConexaoInternet() {
    $url = "https://www.google.com"; // Você pode usar outro URL confiável, se preferir

    // Tente fazer uma solicitação ao URL
    $resultado = @file_get_contents($url);

    // Se a solicitação foi bem-sucedida, há conexão com a internet
    if ($resultado !== false) {
        return true;
    } else {
        return false;
    }
}
class MySql{
		private static $pdo;
		public static function conectar(){
			if(verificaConexaoInternet() == true){

			if (self::$pdo == null){
					// code...
				
				try{
					self::$pdo = new PDO('mysql:host=pro107.dnspro.com.br;dbname=spacemid_mixpvd.mixsalgados','spacemid_luis','G4l01313',array(PDO ::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
					self::$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
					}catch(Exception $e){
						echo '<h2>Erro ao se conectar com o banco de dados!</h2>';
						ReportError::conectar($e);
						echo$e;

				}
			}return self::$pdo;
		}

		}
	}
	class ReportError {
		private static $pdo;
	
		public static function conectar($error) {
			if (verificaConexaoInternet() == true) {
				// Verifica se a conexão já está estabelecida
				if (self::$pdo == null) {
					try {
						// Estabelece a conexão somente se ela ainda não foi criada
						self::$pdo = new PDO('mysql:host=pro107.dnspro.com.br;dbname=spacemid_sistem_adm', 'spacemid_luis', 'G4l01313', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
						self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
						// Prepara a query e executa
						$query = self::$pdo->prepare("INSERT INTO `tb_error_log` (`id`, `message`, `project`, `data`) VALUES (NULL, ?, ?, ?)");
						$query->execute([$error, 'Mix Salgados', date("Y-m-d H:i:s")]);
					} catch (Exception $e) {
						// Em caso de erro na conexão, captura e exibe a exceção
						echo '<h2>Erro ao se conectar com o banco de dados!</h2>';
						echo $e;
					}
				}
				// Retorna a conexão estabelecida ou recém-criada
				return self::$pdo;
			} else {
				// Retorna nulo se a verificação de conexão com a internet falhar
				return null;
			}
		}
	}
	
 ?>