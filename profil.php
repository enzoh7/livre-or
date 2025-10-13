<?php
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Location: connexion.php');
    exit();
}

$message = '';
$error = '';

// Traitement du formulaire de modification du profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_login = cleanInput($_POST['login']);
    $current_password = cleanInput($_POST['current_password']);
    $new_password = cleanInput($_POST['new_password']);
    $confirm_password = cleanInput($_POST['confirm_password']);
    
    // Validation des données
    if (empty($new_login)) {
        $error = "Le login ne peut pas être vide.";
    } elseif (strlen($new_login) < 3) {
        $error = "Le login doit contenir au moins 3 caractères.";
    } else {
        try {
            $pdo = getDbConnection();
            
            // Vérifier le mot de passe actuel si un nouveau mot de passe est fourni
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = "Veuillez saisir votre mot de passe actuel.";
                } elseif (strlen($new_password) < 6) {
                    $error = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
                } elseif ($new_password !== $confirm_password) {
                    $error = "Les nouveaux mots de passe ne correspondent pas.";
                } else {
                    // Vérifier le mot de passe actuel
                    $stmt = $pdo->prepare("SELECT password FROM utilisateurs WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();
                    
                    if (!password_verify($current_password, $user['password'])) {
                        $error = "Mot de passe actuel incorrect.";
                    }
                }
            }
            
            if (empty($error)) {
                // Vérifier si le nouveau login est déjà utilisé par un autre utilisateur
                $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE login = ? AND id != ?");
                $stmt->execute([$new_login, $_SESSION['user_id']]);
                
                if ($stmt->rowCount() > 0) {
                    $error = "Ce login est déjà utilisé par un autre utilisateur.";
                } else {
                    // Mettre à jour le profil
                    if (!empty($new_password)) {
                        // Modifier login et mot de passe
                        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE utilisateurs SET login = ?, password = ? WHERE id = ?");
                        $stmt->execute([$new_login, $hashedPassword, $_SESSION['user_id']]);
                    } else {
                        // Modifier seulement le login
                        $stmt = $pdo->prepare("UPDATE utilisateurs SET login = ? WHERE id = ?");
                        $stmt->execute([$new_login, $_SESSION['user_id']]);
                    }
                    
                    // Mettre à jour la session
                    $_SESSION['user_login'] = $new_login;
                    
                    $message = "Profil mis à jour avec succès !";
                }
            }
        } catch(PDOException $e) {
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Récupérer les informations actuelles de l'utilisateur
try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT login FROM utilisateurs WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $current_login = $user['login'];
} catch(PDOException $e) {
    $current_login = $_SESSION['user_login'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Livre d'Or</title>
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
                    <li><a href="profil.php" class="active">Mon Profil</a></li>
                    <li><a href="commentaire.php">Ajouter un commentaire</a></li>
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                    <li class="user-info">Bonjour, <?php echo htmlspecialchars($_SESSION['user_login']); ?></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h2>Mon Profil</h2>
                <p>Modifiez vos informations personnelles</p>
                
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
                        <label for="login">Nouveau login :</label>
                        <input type="text" id="login" name="login" required 
                               value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : htmlspecialchars($current_login); ?>"
                               placeholder="Votre nom d'utilisateur">
                        <small>Au moins 3 caractères</small>
                    </div>
                    
                    <div class="form-section">
                        <h3>Changer le mot de passe (optionnel)</h3>
                        <p class="form-info">Laissez vide si vous ne souhaitez pas changer votre mot de passe</p>
                        
                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel :</label>
                            <input type="password" id="current_password" name="current_password"
                                   placeholder="Votre mot de passe actuel">
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe :</label>
                            <input type="password" id="new_password" name="new_password"
                                   placeholder="Nouveau mot de passe (au moins 6 caractères)">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmer le nouveau mot de passe :</label>
                            <input type="password" id="confirm_password" name="confirm_password"
                                   placeholder="Répétez le nouveau mot de passe">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Mettre à jour le profil</button>
                </form>
                
                <div class="form-footer">
                    <p><a href="index.php">Retour à l'accueil</a></p>
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
        // Validation en temps réel des mots de passe
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Si on saisit un nouveau mot de passe, rendre obligatoire le mot de passe actuel
        document.getElementById('new_password').addEventListener('input', function() {
            const currentPasswordField = document.getElementById('current_password');
            if (this.value.length > 0) {
                currentPasswordField.required = true;
            } else {
                currentPasswordField.required = false;
            }
        });
    </script>
</body>
</html>