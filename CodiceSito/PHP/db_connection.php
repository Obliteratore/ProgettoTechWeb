<?php
namespace FM;

class FMAccess {

	private const HOST_DB = "localhost";
	private const DATABASE_NAME = "fbalestr";
	private const USERNAME = "fbalestr";
	private const PASSWORD = "Iemao4Chawiechoo";

	private $connection;

	public function openConnection() {

		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

		$this->connection = mysqli_connect(FMAccess::HOST_DB, FMAccess::USERNAME, FMAccess::PASSWORD, FMAccess::DATABASE_NAME);

		if(mysqli_connect_errno()) {
			return false;
		} else {
			return true;
		}
	}

	public function closeConnection() {
		try {
        mysqli_close($this->connection);
		} catch (Throwable $e) {
			
		} finally {
			$this->connection = null;
		}
	}

	public function beginTransaction() {
        mysqli_begin_transaction($this->connection);
    }

    public function commit() {
        mysqli_commit($this->connection);
    }

    public function rollback() {
        mysqli_rollback($this->connection);
    }

	public function getComuni($provincia) {
		$query = "SELECT id_comune, nome FROM comuni WHERE sigla_provincia = ? ORDER BY nome";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("s", $provincia);
		$stmt->execute();

		$result = $stmt->get_result();

		$comuni = [];
		if($result->num_rows !== 0) {
			while($row = $result->fetch_assoc()) {
				$comuni[] = $row;
			}
		}
		$result->free();
		$stmt->close();
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
	
	public function existProvincia($provincia) {
		$query = "SELECT sigla_provincia FROM provincie WHERE sigla_provincia = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("s", $provincia);
		$stmt->execute();

		$result = $stmt->get_result();

		$exist = true;
		if($result->num_rows === 0)
			$exist = false;

		$result->free();
		$stmt->close();
		return $exist;
	}

	public function existComune($comune, $provincia) {
		$query = "SELECT id_comune FROM comuni WHERE id_comune = ? AND sigla_provincia = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("is", $comune, $provincia);
		$stmt->execute();

		$result = $stmt->get_result();

		$exist = true;
		if($result->num_rows === 0)
			$exist = false;

		$result->free();
		$stmt->close();
		return $exist;
	}

	public function existEmail($email) {
		$query = "SELECT email FROM utenti WHERE email = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("s", $email);
		$stmt->execute();

		$result = $stmt->get_result();

		$exist = false;
		if($result->num_rows !== 0)
			$exist = true;

		$result->free();
		$stmt->close();
		return $exist;
	}

	public function existUsername($username) {
		$query = "SELECT username FROM utenti_registrati WHERE username = ? 
		UNION 
		SELECT username FROM amministratori WHERE username = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("ss", $username, $username);
		$stmt->execute();

		$result = $stmt->get_result();

		$exist = false;
		if($result->num_rows !== 0)
			$exist = true;

		$result->free();
		$stmt->close();
		return $exist;
	}

	public function insertUtente($email) {
		$query = "INSERT INTO utenti (email) VALUES (?)";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->close();
	}

	public function insertUtenteRegistrato($email, $username, $password, $nome, $cognome) {
		$hash = password_hash($password, PASSWORD_DEFAULT);

		$query = "INSERT INTO utenti_registrati (email, username, password, nome, cognome) VALUES (?, ?, ?, ?, ?)";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("sssss", $email, $username, $hash, $nome, $cognome);
		$stmt->execute();
		$stmt->close();
	}

	public function insertIndirizzo($provincia, $comune, $via) {
		$query = "INSERT INTO indirizzi (sigla_provincia, id_comune, via) VALUES (?, ?, ?)";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("sis", $provincia, $comune, $via);
		$stmt->execute();

		$idIndirizzo = $this->connection->insert_id;

		$stmt->close();
		return $idIndirizzo;
	}

	public function insertUtenteRegistratoIndirizzo($email, $idIndirizzo) {
		$query = "INSERT INTO utenti_indirizzi (email, id_indirizzo) VALUES (?, ?)";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("si", $email, $idIndirizzo);
		$stmt->execute();
		$stmt->close();
	}

	public function getPasswordWithEmail($email) {
		$query = "SELECT password FROM utenti_registrati WHERE email = ? 
		UNION 
		SELECT password FROM amministratori WHERE email = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("ss", $email, $email);
		$stmt->execute();

		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
    
		$result->free();
		$stmt->close();

		return $row ? $row['password'] : null;
	}

	public function getPasswordWithUsername($username) {
		$query = "SELECT password FROM utenti_registrati WHERE username = ? 
		UNION 
		SELECT password FROM amministratori WHERE username = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("ss", $username, $username);
		$stmt->execute();

		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
    
		$result->free();
		$stmt->close();

		return $row ? $row['password'] : null;
	}

	public function getDatiUtenteRegistrato($email) {
		$query = "SELECT username, nome, cognome FROM utenti_registrati WHERE email = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("s", $email);
		$stmt->execute();

		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
    
		$result->free();
		$stmt->close();

		return $row;
	}
}
?>