<?php

class MySql
{
	private static $pdo;
	public static function conectar()
	{


		if (self::$pdo == null) {
			// code...

			try {
				self::$pdo = new PDO('mysql:host=pro107.dnspro.com.br;dbname=spacemid_mixpvd.mixsalgados', 'spacemid_luis', 'G4l01313', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (Exception $e) {
				echo '<h2>Erro ao se conectar com o banco de dados!</h2>';
				echo $e;
			}
		}
		return self::$pdo;
	}
}
class ReportError
{
	public static function conectar($error, $email)
	{
		// Construa a URL com os parâmetros necessários
		$url = 'https://super-error-log-git-main-luisgustavorrs-projects.vercel.app/monitorar-get?' . http_build_query([
			'Sistema' => 'MixSalgados',
			'Error' => $error,
			'Email' => $email,
		]);

		// Inicialize o cURL
		$ch = curl_init($url);

		// Configurações adicionais do cURL
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			// Adicione outras opções conforme necessário
		]);

		// Execute a requisição cURL
		$response = curl_exec($ch);

		// Lógica para lidar com a resposta, se necessário
		echo 'Resposta recebida: ' . $response;

		// Verifique por erros
		if (curl_errno($ch)) {
			echo 'Erro cURL: ' . curl_error($ch);
		}

		// Feche o handle cURL
		curl_close($ch);
	}
}
