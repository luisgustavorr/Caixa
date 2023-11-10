<?php 

class MySql{
		private static $pdo;
		public static function conectar(){


			if (self::$pdo == null){
					// code...
				
				try{
					self::$pdo = new PDO('mysql:host=pro107.dnspro.com.br;dbname=spacemid_mixpvd.mixsalgados','spacemid_luis','G4l01313',array(PDO ::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
					self::$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
					}catch(Exception $e){
						echo '<h2>Erro ao se conectar com o banco de dados!</h2>';
						echo$e;

				}
			}return self::$pdo;
		

		}
	}
	class ReportError {

	
		public static function conectar($error,$email) {

				// Verifica se a conexão já está estabelecida
				$dadosParaEnviar = http_build_query(
					array(
						'Sistema' => 'MixSalgados',
						'Erro' => $error,
						'Email' => $email
					)
				);
				
				$opcoes = array(
					'http' =>
					array(
						'method'  => 'POST',
						'header'  => 'Content-Type: application/x-www-form-urlencoded',
						'content' => $dadosParaEnviar
					)
				);
				
				$contexto = stream_context_create($opcoes);
				$resultado = file_get_contents('http://localhost:3000/monitorar-post', false, $contexto);
				
		
		}
	}
	
 ?>