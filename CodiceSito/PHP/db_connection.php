<?php
namespace FM;

class FMAccess {

	private const HOST_DB = "localhost";
	private const DATABASE_NAME = "agingill";
	private const USERNAME = "agingill";
	private const PASSWORD = "Pech3pheeXie4xen";

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

	public function insertIndirizzo($provincia, $comune, $via) {
		$query = "INSERT INTO indirizzi (sigla_provincia, id_comune, via) VALUES (?, ?, ?)";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("sis", $provincia, $comune, $via);
		$stmt->execute();

		$idIndirizzo = $this->connection->insert_id;

		$stmt->close();
		return $idIndirizzo;
	}

	public function insertUtente($email, $idIndirizzo) {
		$query = "INSERT INTO utenti (email, id_indirizzo) VALUES (?, ?)";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("si", $email, $idIndirizzo);
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

	public function getPesci() {
		//purificazione dell'input
		$nomeLatino = filter_input(INPUT_GET, 'nome_latino', FILTER_SANITIZE_SPECIAL_CHARS);
		$nomeComune = filter_input(INPUT_GET, 'nome_comune', FILTER_SANITIZE_SPECIAL_CHARS);
		$dimensione = filter_input(INPUT_GET, 'dimensione', FILTER_VALIDATE_INT);
		$volumeMinimo = filter_input(INPUT_GET, 'volumeMinimo', FILTER_VALIDATE_INT);
		$colori = $_GET['color'] ?? [];
		$nomeComune = filter_input(INPUT_GET, 'nome_comune', FILTER_SANITIZE_SPECIAL_CHARS);
		$prezzoMinimo = filter_input(INPUT_GET, 'prezzoMinimo', FILTER_VALIDATE_INT);
		$prezzoMassimo = filter_input(INPUT_GET, 'prezzoMassimo', FILTER_VALIDATE_INT);

		$condizioni = [];
		$parametri = [];

		if (!empty($_GET['nome_latino'])) {
			$condizioni[] = "nome_latino LIKE :nome_latino";
			$parametri[':nome_latino'] = '%' . $_GET['nome_latino'] . '%';
		}

		if (!empty($_GET['nome_comune'])) {
			$condizioni[] = "nome_comune LIKE :nome_comune";
			$parametri[':nome_comune'] = '%'. $_GET['nome_comune'] . '%';
		}

		if (!empty($_GET['dimensione'])) {
			$condizioni[] = "dimensione = dimensione";
			$parametri[':dimensione'] = $_GET['dimensione'];
		}	

		if (!empty($_GET['volume_minimo'])) {
			$condizioni[] = "volume_minimo >= :volume_minimo";
			$parametri[':volume_minimo'] = $_GET['volume_minimo'];
		}
		if (!empty($colori)) {
			foreach ($colori as $i => $colore) {
				$condizioni[] = "FIND_IN_SET(:c$i, colori)";
				$parametri[":c$i"] = $colore;
			}
		}	
		if (!empty($_GET['prezzo_min'])) {
			$condizioni[] = "prezzo >= :prezzo_min";
			$parametri[':prezzo_min'] = $_GET['prezzo_min'];
		}

		if (!empty($_GET['prezzo_max'])) {
			$condizioni[] = "prezzo >= :prezzo_max";
			$parametri[':prezzo_max'] = $_GET['prezzo_max'];
		}

		$sql = "SELECT * FROM pesci";

		if ($condizioni) {
			$sql .= " WHERE " . implode(" AND ", $condizioni);
		}

		$stmt = $this->$connection->prepare($sql);
		$stmt->execute($parametri);

		$query = "SELECT * FROM pesci ORDER BY ID ASC";
		$queryResult = mysqli_query($this->connection,$query) or die("Errore in dbConnection: " .mysqli_error($this->connection));

		if(mysqli_num_rows($queryResult) != 0) {
			$result = array();
			while($row = mysqli_fetch_assoc($queryResult)) {
				array_push($result, $row);
			}
			$queryResult->free();
			return $result;
		} else {
			return false;
		}

	}


	public function getProfiloUtente($email) {
		$query = "SELECT utenti_registrati.username, utenti_registrati.nome, utenti_registrati.cognome, provincie.sigla_provincia, provincie.nome AS provincia, comuni.nome AS comune, indirizzi.via FROM 
		utenti_registrati JOIN utenti ON utenti_registrati.email=utenti.email
		JOIN indirizzi ON utenti.id_indirizzo=indirizzi.id_indirizzo 
		JOIN provincie ON indirizzi.sigla_provincia=provincie.sigla_provincia 
		JOIN comuni ON indirizzi.id_comune=comuni.id_comune 
		WHERE utenti_registrati.email = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("s", $email);
		$stmt->execute();

		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
    
		$result->free();
		$stmt->close();

		return $row;
	}

	public function getOrdiniUtente($email) {
		$query = "SELECT ordini.id_ordine, ordini.id_indirizzo, ordini.data_ora, pesci.nome_comune, dettaglio_ordini.prezzo_unitario, dettaglio_ordini.quantita FROM 
		ordini JOIN dettaglio_ordini ON ordini.id_ordine=dettaglio_ordini.id_ordine 
		JOIN pesci ON dettaglio_ordini.nome_latino=pesci.nome_latino 
		WHERE ordini.email= ? 
		ORDER BY ordini.id_ordine";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("s", $email);
		$stmt->execute();

		$result = $stmt->get_result();

		$ordini = [];
		if($result->num_rows !== 0) {
			while($row = $result->fetch_assoc()) {
				$ordini[] = $row;
			}
		}
    
		$result->free();
		$stmt->close();

		return $ordini;
	}

	public function getPesci($condizioni, $parametri) {

		$sql = "SELECT * FROM pesci";

		if ($condizioni) {
			$sql .= " WHERE " . implode(" AND ", $condizioni);
		}

		$stmt = $this->$connection->prepare($sql);
		$stmt->execute($parametri);

		$result = $stmt->get_result();

		$pesci = [];
		if($result->num_rows !== 0) {
			while($row = $result->fetch_assoc()) {
				$pesci[] = $row;
			}
		}
		$result->free();
		$stmt->close();
		return $pesci;
	}
}
?>