<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livre d'Or - Accueil</title>
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
                    <?php if (isLoggedIn()): ?>
                        <li><a href="profil.php">Mon Profil</a></li>
                        <li><a href="commentaire.php">Ajouter un commentaire</a></li>
                        <li><a href="deconnexion.php">Déconnexion</a></li>
                        <li class="user-info">Bonjour, <?php echo htmlspecialchars($_SESSION['user_login']); ?></li>
                    <?php else: ?>
                        <li><a href="connexion.php">Connexion</a></li>
                        <li><a href="inscription.php">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <section class="hero">
                <h2>Bienvenue sur notre Livre d'Or</h2>
                <p>Partagez vos expériences et découvrez les avis de nos visiteurs</p>
            </section>

            <section class="features">
                <div class="feature-grid">
                    <div class="feature-card">
                        <h3>📝 Partagez votre avis</h3>
                        <p>Inscrivez-vous et laissez un commentaire pour partager votre expérience avec notre communauté.</p>
                        <?php if (!isLoggedIn()): ?>
                            <a href="inscription.php" class="btn btn-primary">S'inscrire</a>
                        <?php else: ?>
                            <a href="commentaire.php" class="btn btn-primary">Ajouter un commentaire</a>
                        <?php endif; ?>
                    </div>

                    <div class="feature-card">
                        <h3>👥 Rejoignez la communauté</h3>
                        <p>Découvrez les témoignages et commentaires laissés par nos autres visiteurs.</p>
                        <a href="livre-or.php" class="btn btn-secondary">Voir le livre d'or</a>
                    </div>

                    <div class="feature-card">
                        <h3>🎨 Interface moderne</h3>
                        <p>Profitez d'une interface claire et intuitive pour naviguer facilement sur le site.</p>
                        <?php if (!isLoggedIn()): ?>
                            <a href="connexion.php" class="btn btn-secondary">Se connecter</a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="stats">
                <h3>Statistiques du site</h3>
                <div class="stats-grid">
                    <?php
                    try {
                        $pdo = getDbConnection();
                        
                        // Compter les utilisateurs
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM utilisateurs");
                        $usersCount = $stmt->fetch()['count'];
                        
                        // Compter les commentaires
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM commentaires");
                        $commentsCount = $stmt->fetch()['count'];
                        
                        // Dernier commentaire
                        $stmt = $pdo->query("SELECT date FROM commentaires ORDER BY date DESC LIMIT 1");
                        $lastComment = $stmt->fetch();
                        $lastCommentDate = $lastComment ? date('d/m/Y', strtotime($lastComment['date'])) : 'Aucun';
                        
                    } catch(PDOException $e) {
                        $usersCount = 0;
                        $commentsCount = 0;
                        $lastCommentDate = 'N/A';
                    }
                    ?>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $usersCount; ?></span>
                        <span class="stat-label">Utilisateurs inscrits</span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $commentsCount; ?></span>
                        <span class="stat-label">Commentaires postés</span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $lastCommentDate; ?></span>
                        <span class="stat-label">Dernier commentaire</span>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Mon Livre d'Or. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>