<?php
namespace FM;

class FMAccess {

	private const HOST_DB = "localhost";
	private const DATABASE_NAME = "fbalestr";
	private const USERNAME = "fbalestr";
	private const PASSWORD = "Iemao4Chawiechoo";

	private $connection;

	public function openConnection() {

		mysqli_report(MYSQLI_REPORT_ERROR);

		$this->connection = mysqli_connect(FMAccess::HOST_DB, FMAccess::USERNAME, FMAccess::PASSWORD, FMAccess::DATABASE_NAME);

		if(mysqli_connect_errno()) {
			return false;
		} else {
			return true;
		}
	}

	public function closeConnection() {
		mysqli_close($this->connection);
	}

	public function getComuni($provincia) {
		$query = "SELECT id_comune, nome FROM comuni WHERE sigla_provincia = ? ORDER BY nome";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("s", $provincia);
		$stmt->execute();

		$result = $stmt->get_result();

		$comuni = [];
		while ($row = $result->fetch_assoc()) {
			$comuni[] = $row;
		}
		$result->free();
		return $comuni;
	}

	public function getPesce($nome_latino) {
		$query = "SELECT p.* , f.tipo_acqua FROM pesci p JOIN famiglie f ON p.famiglia = f.famiglia_latino WHERE nome_latino = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("s", $nome_latino);
		$stmt->execute();

		$result = $stmt->get_result();
		$pesce = $result->fetch_assoc();
		if($result) $result->free();
		$stmt->close();
		return $pesce;
	}
}
?>