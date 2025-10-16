<?php
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Location: connexion.php');
    exit();
}

$message = '';
$error = '';

// Traitement du formulaire d'ajout de commentaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commentaire = cleanInput($_POST['commentaire']);
    
    if (empty($commentaire)) {
        $error = "Le commentaire ne peut pas être vide.";
    } elseif (strlen($commentaire) < 10) {
        $error = "Le commentaire doit contenir au moins 10 caractères.";
    } elseif (strlen($commentaire) > 1000) {
        $error = "Le commentaire ne peut pas dépasser 1000 caractères.";
    } else {
        try {
            $pdo = getDbConnection();
            
            // Insérer le commentaire avec la date et l'heure actuelles
            $dateActuelle = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("INSERT INTO commentaires (commentaire, id_utilisateur, date) VALUES (?, ?, ?)");
            $stmt->execute([$commentaire, $_SESSION['user_id'], $dateActuelle]);
            
            $message = "Votre commentaire a été ajouté avec succès !";
            
            // Redirection vers le livre d'or après 2 secondes
            header("refresh:2;url=livre-or.php");
        } catch(PDOException $e) {
            $error = "Erreur lors de l'ajout du commentaire : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un commentaire - Livre d'Or</title>
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
                    <li><a href="profil.php">Mon Profil</a></li>
                    <li><a href="commentaire.php" class="active">Ajouter un commentaire</a></li>
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                    <li class="user-info">Bonjour, <?php echo htmlspecialchars($_SESSION['user_login']); ?></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h2>✍️ Ajouter un commentaire</h2>
                <p>Partagez votre expérience avec notre communauté</p>
                
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
                        <label for="commentaire">Votre commentaire :</label>
                        <textarea id="commentaire" name="commentaire" required rows="8"
                                  placeholder="Partagez votre expérience, vos impressions, vos suggestions... (minimum 10 caractères)"><?php echo isset($_POST['commentaire']) ? $_POST['commentaire'] : ''; ?></textarea>
                        <div class="character-counter">
                            <span id="char-count">0</span> / 1000 caractères
                            <span id="char-min">(minimum 10)</span>
                        </div>
                    </div>
                    
                    <div class="form-info">
                        <p><strong>Conseils pour un bon commentaire :</strong></p>
                        <ul>
                            <li>Soyez précis et constructif</li>
                            <li>Partagez votre expérience personnelle</li>
                            <li>Respectez les autres utilisateurs</li>
                            <li>Évitez les propos offensants</li>
                        </ul>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            📝 Publier le commentaire
                        </button>
                        <a href="livre-or.php" class="btn btn-secondary">
                            📖 Voir le livre d'or
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Mon Livre d'Or. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        // Compteur de caractères et validation en temps réel
        const textarea = document.getElementById('commentaire');
        const charCount = document.getElementById('char-count');
        const charMin = document.getElementById('char-min');
        const submitBtn = document.getElementById('submit-btn');
        
        function updateCharacterCount() {
            const length = textarea.value.length;
            charCount.textContent = length;
            
            // Changer la couleur selon la longueur
            if (length < 10) {
                charCount.style.color = '#e74c3c';
                charMin.style.display = 'inline';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
            } else if (length > 1000) {
                charCount.style.color = '#e74c3c';
                charMin.style.display = 'none';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
            } else {
                charCount.style.color = '#27ae60';
                charMin.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }
        }
        
        textarea.addEventListener('input', updateCharacterCount);
        textarea.addEventListener('keyup', updateCharacterCount);
        
        // Vérification initiale
        updateCharacterCount();
        
        // Auto-resize du textarea
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    </script>
</body>
</html>