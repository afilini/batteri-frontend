<?PHP

$root = realpath($_SERVER["DOCUMENT_ROOT"]) . "/bcms";

require_once("$root/libs/MysqliDb.php");
require_once("$root/config.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db = new MysqliDb (Array (
                'host' => $_CONFIG['db_host'],
                'username' => $_CONFIG['db_user'],
                'password' => $_CONFIG['db_password'],
                'db' => $_CONFIG['db_name'],
                'port' => $_CONFIG['db_port']));

function isLoggedIn () {
    if (isset($_SESSION['email']) && isset($_SESSION['password'])) {
        $email = $_SESSION['email'];
        $password = $_SESSION['password'];

        $user = new User($email);
        $user->passwordHash = $password;

        $nickname = $user->load();

        if ($nickname)
            return $user;
	}

    return false;
}

function logout () {
    $_SESSION['email'] = NULL;
    $_SESSION['password'] = NULL;
}

function login ($email, $password) {
    $user = new User($email);
    $user->passwordHash = hash('sha256', $password);

    $result = $user->load();
    if ($result) {
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $user->passwordHash;

        return true;
    }

    logout();
    return false;
}

class User {
	public $nickname = "";
	public $email = "";

	public $passwordHash = "";

	public $dbID = -1;
	public $punti = 0;

	public function __construct($email) {
        $this->email = $email;
    }

    public function setPassword ($password) {
    	$this->passwordHash = hash('sha256', $password);
    }

    public function save () {
    	global $db;

    	$data = Array (
    			"nickname" => $this->nickname,
               	"email" => $this->email,
               	"password" => $this->passwordHash);

    	if ($this->dbID != -1) { // Update
    		return $db->where('ID', $this->dbID)->update('users', $data);
    	} else { // Insert
			return $db->insert('users', $data);
    	}
    }

    public function load () {
    	global $db;

    	$result = $db->where('email', $this->email)->getOne('users');

    	$this->passwordHash = $result['password'];
    	$this->nickname = $result['nickname'];

    	$this->punti = $result['punti'];
    	$this->dbID = $result['ID'];

        return $this->nickname;
    }
}