<?php require __DIR__ . '/../partials/nav.php'; ?>

<div class="container">
    <h1>Nouvel adherent</h1>

    <?php if ($erreur): ?><div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

    <form class="card" method="post" action="index.php?page=adherents&action=create">
        <label>Nom</label>
        <input type="text" name="nom" required>

        <label>Prenom</label>
        <input type="text" name="prenom" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Telephone</label>
        <input type="text" name="telephone">

        <label>Salle d'inscription</label>
        <select name="id_salle" required>
            <?php foreach ($salles as $s): ?>
                <option value="<?= $s['id_salle'] ?>"><?= htmlspecialchars($s['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Enregistrer</button>
    </form>

    <a class="btn-link" href="index.php?page=adherents">&larr; Retour a la liste</a>
</div>
