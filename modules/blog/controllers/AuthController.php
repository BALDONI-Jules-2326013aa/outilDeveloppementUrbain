class AuthController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? 'login';

        switch ($action) {
            case 'register':
                $this->handleRegister();
                break;
            case 'login':
            default:
                $this->handleLogin();
                break;
        }
    }

    private function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($password !== $confirmPassword) {
                $error = "Les mots de passe ne correspondent pas.";
                require 'views/registerView.php';
                return;
            }

            if ($this->userModel->register($email ,$username, $password)) {
                header("Location: ?action=login&registered=1");
                exit;
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }

        require 'views/registerView.php';
    }

    private function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->login($username, $password);

            if ($user) {
                session_start();
                $_SESSION['user'] = $user;
                header("Location: ");
                exit;
            } else {
                $error = "Identifiants incorrects.";
            }
        }

        require 'views/loginView.php';
    }
}