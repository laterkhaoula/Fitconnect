<?php require __DIR__ . '/../partials/nav.php'; ?>

<div class="container">
    <h1>Nouvel abonnement</h1>

    <?php if ($erreur): ?><div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

    <form class="card" method="post" action="index.php?page=abonnements&action=create">
        <label>Adherent</label>
        <select name="id_adherent" required>
            <?php foreach ($adherents as $a): ?>
                <option value="<?= $a->getIdAdherent() ?>"><?= htmlspecialchars($a->getNomComplet()) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Type d'abonnement</label>
        <select name="type" required>
            <option value="mensuel">Mensuel</option>
            <option value="trimestriel">Trimestriel</option>
            <option value="annuel">Annuel</option>
        </select>

        <label>Date de debut (optionnel, defaut = aujourd'hui)</label>
        <input type="date" name="date_debut">

        <button type="submit">Souscrire</button>
    </form>

    <p style="font-size:.85rem;color:#52606d;max-width:480px;">
        Si l'adherent selectionne possede deja un abonnement actif, celui-ci sera
        automatiquement resilie avant la creation du nouveau (regle : un seul
        abonnement actif a la fois).
    </p>

    <a class="btn-link" href="index.php?page=abonnements">&larr; Retour a la liste</a>
</div>
