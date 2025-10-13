<?php
require_once 'config.php';

// Récupération des commentaires avec les informations des utilisateurs
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("
        SELECT c.commentaire, c.date, u.login 
        FROM commentaires c 
        JOIN utilisateurs u ON c.id_utilisateur = u.id 
        ORDER BY c.date DESC
    ");
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $commentaires = [];
    $db_error = "Erreur lors du chargement des commentaires : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livre d'Or</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-container">
                <h1><a href="index.php">Mon Livre d'Or</a></h1>
                <ul class="nav-menu">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="livre-or.php" class="active">Livre d'Or</a></li>
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
            <div class="guestbook-header">
                <h2>📖 Livre d'Or</h2>
                <p>Découvrez les témoignages et avis de nos visiteurs</p>
                
                <?php if (isLoggedIn()): ?>
                    <div class="action-buttons">
                        <a href="commentaire.php" class="btn btn-primary">✍️ Ajouter votre commentaire</a>
                    </div>
                <?php else: ?>
                    <div class="action-buttons">
                        <p class="login-prompt">
                            <a href="connexion.php" class="btn btn-primary">Se connecter</a> 
                            pour laisser un commentaire
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (isset($db_error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($db_error); ?>
                </div>
            <?php endif; ?>

            <div class="comments-section">
                <?php if (empty($commentaires)): ?>
                    <div class="no-comments">
                        <div class="no-comments-icon">💬</div>
                        <h3>Aucun commentaire pour le moment</h3>
                        <p>Soyez le premier à partager votre expérience !</p>
                        <?php if (!isLoggedIn()): ?>
                            <p><a href="inscription.php" class="btn btn-secondary">S'inscrire maintenant</a></p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="comments-count">
                        <h3><?php echo count($commentaires); ?> commentaire<?php echo count($commentaires) > 1 ? 's' : ''; ?></h3>
                    </div>
                    
                    <div class="comments-list">
                        <?php foreach ($commentaires as $index => $commentaire): ?>
                            <article class="comment-card <?php echo $index % 2 == 0 ? 'comment-even' : 'comment-odd'; ?>">
                                <div class="comment-header">
                                    <div class="comment-meta">
                                        <span class="comment-date">
                                            📅 Posté le <?php echo date('d/m/Y à H:i', strtotime($commentaire['date'])); ?>
                                        </span>
                                        <span class="comment-author">
                                            👤 par <strong><?php echo htmlspecialchars($commentaire['login']); ?></strong>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="comment-content">
                                    <p><?php echo nl2br(htmlspecialchars($commentaire['commentaire'])); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (count($commentaires) > 0): ?>
                <div class="guestbook-footer">
                    <div class="stats-summary">
                        <p>
                            <strong><?php echo count($commentaires); ?></strong> témoignage<?php echo count($commentaires) > 1 ? 's' : ''; ?> 
                            de nos visiteurs
                        </p>
                        <?php if (isLoggedIn()): ?>
                            <p><a href="commentaire.php" class="btn btn-primary btn-small">Ajouter le vôtre</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Mon Livre d'Or. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>