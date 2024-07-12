<?php
function ta(mixed $in):void {
	if(TESTMODUS) {
		echo('<pre class="ta">');
		print_r($in);
		echo('</pre>');
	}
}
define("TESTMODUS",true);

define("DB",[
	"host" => "localhost",
	"user" => "root",
	"pwd" => "",
	"name" => "db_lap_reisetagebuch"
]);
function dbConnect():mysqli {
	try {
		$conn_intern = new MySQLi(DB["host"],DB["user"],DB["pwd"],DB["name"]);
		if($conn_intern->connect_errno>0) {
			if(TESTMODUS) {
				die("Fehler im Verbindungsaufbau. Abbruch");
			}
			else {
				header("Location: errors/db_connect.html");
			}
		}
		$conn_intern->set_charset("utf8mb4");
	}
	catch(Exception $e) {
		ta("Fehler im Verbindungsaufbau: ".$conn_intern->connect_error);
		if(TESTMODUS) {
			die("Fehler im Verbindungsaufbau. Abbruch");
		}
		else {
			header("Location: errors/db_connect.html"); 
		}
	}
	
	return $conn_intern;
}

function dbQuery(mysqli $conn_intern, string $sql):mysqli_result|bool {
	try {
		$daten = $conn_intern->query($sql);
		if($daten===false) {
			if(TESTMODUS) {
				ta($sql);
				die("Fehler im SQL-Statement. Abbruch: " . $conn_intern->error);
			}
			else {
				header("Location: errors/db_query.html");
			}
		}
	}
	catch(Exception $e) {
		if(TESTMODUS) {
			die("Fehler im SQL-Statement. Abbruch: " . $conn_intern->error);
		}
		else {
			header("Location: errors/db_query.html");
		}
	}
	
	return $daten;
}
$conn=dbConnect();
$msg = ""; 


if(count($_POST)>0) {
    if(isset($_POST["btnLogin"])){
        if(strlen($_POST["E"])>0 && strlen($_POST["P"])) {
            //----------- Login ---------------
            $sql='
                SELECT 
                    *
                FROM tbl_user
                WHERE (
                    Emailadresse="'.$conn->real_escape_string($_POST["E"]).'"
                )
            ';
            $login=dbQuery($conn, $sql);
            if($login->num_rows==1){
                $log=$login->fetch_object();
                if($conn->real_escape_string($_POST["E"])==$log->Emailadresse && password_verify($_POST["P"],$log->Passwort)){
                    session_start();
                    $_SESSION["eingeloggt"] = true; 
                    $_SESSION["UID"]= $log->IDUser;
                    $msg="Erfolgreich eingeloggt";
                    header("Location: logged.php");
                }
                else{
                    $msg="Eingegebene Daten sind nicht korrekt";
                }
            }
            else{
              
            }


        }
        else {
            //der Login ist nicht korrekt -> Meldung an den User
            $msg = '<p class="error">Leider waren die eingegebenen Daten nicht korrekt.</p>';
        }
    }
    //----------- Registrierung ----------------------
    if(isset($_POST["btnReg"])){
        $nach=NULL;
        $beschreibung=NULL;
        $sql='
            SELECT 
                IDUser
            FROM tbl_user
            WHERE Emailadresse="' . $conn->real_escape_string($_POST["EA"]) . '"
        ';

        $check=dbQuery($conn,$sql);
        if($check->num_rows ==0){
            if(strlen($_POST["NNA"])>0){
                $nach='"'.$_POST["NNA"].'"';
            }
            if(strlen($_POST["BA"])>0){
                $beschreibung='"'.$_POST["BA"].'"';
            }           
            $sql = '
            INSERT INTO tbl_user
                (Emailadresse, Passwort, Vorname, Nachname, Beschreibung)
            VALUES (
                "' . $conn->real_escape_string($_POST["EA"]) . '",
                "' . password_hash($_POST["PA"],PASSWORD_DEFAULT) . '",
                "'.$_POST["NNA"].'",
                '.$nach.',
                '.$beschreibung.'
                )
            ';
            echo($sql);
            $ok=dbQuery($conn,$sql);
            if($ok){
                    $sql='
                    SELECT 
                        *
                    FROM tbl_user
                    WHERE (
                        Emailadresse="'.$conn->real_escape_string($_POST["EA"]).'"
                    )
                ';
                $login=dbQuery($conn, $sql);
                if($login->num_rows==1){
                    $log=$login->fetch_object();
                    if($conn->real_escape_string($_POST["EA"])==$log->Emailadresse && password_verify($_POST["PA"],$log->Passwort)){
                        session_start();
                        $_SESSION["eingeloggt"] = true; 
                        $_SESSION["UID"]= $log->IDUser;
                        $msg="Erfolgreich eingeloggt";
                        header("Location: logged.php");
                    }
                    else{
                        $msg="Eingegebene Daten sind nicht korrekt";
                    }
                }
                else{
                    $msg="Error";
                }
            }
        }
    }
}
ta($_POST);
?>