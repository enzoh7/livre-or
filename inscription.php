<?php
require_once 'config.php';

$message = '';
$error = '';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = cleanInput($_POST['login']);
    $password = cleanInput($_POST['password']);
    $confirm_password = cleanInput($_POST['confirm_password']);
    
    // Validation des données
    if (empty($login) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (strlen($login) < 3) {
        $error = "Le login doit contenir au moins 3 caractères.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            $pdo = getDbConnection();
            
            // Vérifier si le login existe déjà
            $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE login = ?");
            $stmt->execute([$login]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Ce login est déjà utilisé.";
            } else {
                // Hasher le mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insérer le nouvel utilisateur
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (login, password) VALUES (?, ?)");
                $stmt->execute([$login, $hashedPassword]);
                
                $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                
                // Redirection vers la page de connexion après 2 secondes
                header("refresh:2;url=connexion.php");
            }
        } catch(PDOException $e) {
            $error = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Livre d'Or</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-container">
                <h1><a href="index.php">Mon Livre d'Or</a></h1>
                <ul class="nav-menu">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="livre-or.php">Livre d'Or</a></li>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="inscription.php" class="active">Inscription</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h2>Inscription</h2>
                <p>Créez votre compte pour participer au livre d'or</p>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="form">
                    <div class="form-group">
                        <label for="login">Login :</label>
                        <input type="text" id="login" name="login" required 
                               value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>"
                               placeholder="Choisissez votre nom d'utilisateur">
                        <small>Au moins 3 caractères</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe :</label>
                        <input type="password" id="password" name="password" required
                               placeholder="Choisissez un mot de passe sécurisé">
                        <small>Au moins 6 caractères</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe :</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               placeholder="Répétez votre mot de passe">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
                </form>
                
                <div class="form-footer">
                    <p>Déjà inscrit ? <a href="connexion.php">Connectez-vous ici</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Mon Livre d'Or. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        // Validation en temps réel
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>