<?php
// Configurações do SQLite
$sqlite_db = 'seu_banco_de_dados_sqlite.db';

// Conexão com o SQLite
try {
    $pdo = new PDO("sqlite:$sqlite_db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o SQLite: " . $e->getMessage());
}

// Consulta para selecionar todos os dados da tabela SQLite
$select_query = "SELECT * FROM sua_tabela_sqlite";

try {
    $result = $pdo->query($select_query);

    if ($result) {
        // Exibir os dados da tabela
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Coluna1</th><th>Coluna2</th><th>Coluna3</th></tr>";
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['coluna1'] . "</td>";
            echo "<td>" . $row['coluna2'] . "</td>";
            echo "<td>" . $row['coluna3'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum dado encontrado na tabela.";
    }
} catch (PDOException $e) {
    die("Erro ao consultar a tabela SQLite: " . $e->getMessage());
}

// Fechar a conexão
$pdo = null;
?>
