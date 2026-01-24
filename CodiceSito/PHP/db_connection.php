<?php
namespace FM;

class FMAccess {
	/*
	private const HOST_DB = "localhost";
	private const DATABASE_NAME = "agingill";
	private const USERNAME = "agingill";
	private const PASSWORD = "Pech3pheeXie4xen";
	/*
	/*
	*/
	/*
	private const HOST_DB = "localhost";
	private const DATABASE_NAME = "fbalestr";
	private const USERNAME = "fbalestr";
	private const PASSWORD = "Iemao4Chawiechoo";
	*/
	
	private const HOST_DB = "localhost";
	private const DATABASE_NAME = "bsabic";
	private const USERNAME = "bsabic";
	private const PASSWORD = "ieGai9om6eiyahT0";
	
	/*
	
	private const HOST_DB = "localhost";
	private const DATABASE_NAME = "vsolito";
	private const USERNAME = "vsolito";
	private const PASSWORD = "aeyoh5naiw7nah4S";
	*/

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
		$query = "SELECT email FROM utenti_registrati WHERE email = ? 
		UNION 
		SELECT email FROM amministratori WHERE email = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("ss", $email, $email);
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
		$query = "SELECT p.* , f.tipo_acqua FROM pesci p JOIN famiglie f ON p.famiglia = f.famiglia_latino WHERE p.nome_latino = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("s", $nome_latino);
		$stmt->execute();

		$result = $stmt->get_result();
		$pesce = $result->fetch_assoc();
		
		$result->free();
		$stmt->close();
		return $pesce;
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

	public function getEmailWithUsername($username) {
		$query = "SELECT email FROM utenti_registrati WHERE username = ? 
		UNION 
		SELECT email FROM amministratori WHERE username = ?";
		$stmt = ($this->connection)->prepare($query);
		$stmt->bind_param("ss", $username, $username);
		$stmt->execute();

		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
    
		$result->free();
		$stmt->close();

		return $row ? $row['email'] : null;
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
		$query = "SELECT ordini.id_ordine, ordini.data_ora, indirizzi.via, provincie.sigla_provincia, provincie.nome AS provincia, comuni.nome AS comune, pesci.nome_comune, dettaglio_ordini.prezzo_unitario, dettaglio_ordini.quantita FROM 
		ordini JOIN indirizzi ON ordini.id_indirizzo=indirizzi.id_indirizzo 
		JOIN provincie ON indirizzi.sigla_provincia=provincie.sigla_provincia 
		JOIN comuni ON indirizzi.id_comune=comuni.id_comune 
		JOIN dettaglio_ordini ON ordini.id_ordine=dettaglio_ordini.id_ordine 
		JOIN pesci ON dettaglio_ordini.nome_latino=pesci.nome_latino 
		WHERE ordini.email= ? 
		ORDER BY ordini.id_ordine DESC";
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

	public function getPesci($condizioni, $parametri, $operazione) {

		$sql = "SELECT * FROM pesci";

		if (!empty($condizioni)) {
			$sql .= " WHERE " . implode(" AND ", $condizioni);
		}

		//con questo possiamo usare LIMIT e DESC
		if ($operazione !== '') {
			$sql .= " " . $operazione;
		}
		$stmt = $this->connection->prepare($sql);

		// se ci sono parametri, li bindiamo tutti come stringhe semplicemente
		if (!empty($parametri)) {
			// crea una stringa di 's' lunga quanto il numero di parametri
			$types = str_repeat('s', count($parametri));
			$stmt->bind_param($types, ...$parametri);
		}

		$stmt->execute();
		$result = $stmt->get_result();

		$pesci = [];
		while ($row = $result->fetch_assoc()) {
			$pesci[] = $row;
		}

		$result->free();
		$stmt->close();

		return $pesci;
	}

	public function getPiuVenduti(int $limit = 4): array {
    $limit = (int)$limit;

    $sql = "
        SELECT p.nome_latino, p.nome_comune, p.famiglia, p.dimensione, p.volume_minimo,
               p.colori, p.prezzo, p.sconto_percentuale, p.disponibilita, p.descrizione,
               p.immagine, p.data_inserimento,
               totals.totale_venduto
        FROM pesci p
        JOIN (
            SELECT nome_latino, SUM(quantita) AS totale_venduto
            FROM dettaglio_ordini
            GROUP BY nome_latino
        ) totals ON TRIM(p.nome_latino) = TRIM(totals.nome_latino)
        ORDER BY totals.totale_venduto DESC
        LIMIT $limit
    ";

    $result = $this->connection->query($sql);
    if (!$result) {
        throw new Exception("SQL Error: " . $this->connection->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
	}
}
?>