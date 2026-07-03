<?php require __DIR__ . '/../partials/nav.php'; ?>

<div class="container">
    <h1>Tableau de bord</h1>
    <p class="subtitle">Enregistrement et suivi des seances du reseau</p>

    <?php if ($succes): ?><div class="alert alert-success"><?= htmlspecialchars($succes) ?></div><?php endif; ?>
    <?php if ($erreur): ?><div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

    <form class="card" method="post" action="index.php?page=dashboard">
        <label>Adherent</label>
        <select name="id_adherent" required>
            <?php foreach ($adherents as $a): ?>
                <option value="<?= $a->getIdAdherent() ?>"><?= htmlspecialchars($a->getNomComplet()) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Salle</label>
        <select name="id_salle" required>
            <?php foreach ($salles as $s): ?>
                <option value="<?= $s['id_salle'] ?>"><?= htmlspecialchars($s['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Activite</label>
        <select name="id_activite" required>
            <?php foreach ($activites as $act): ?>
                <option value="<?= $act['id_activite'] ?>"><?= htmlspecialchars($act['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Equipement (optionnel)</label>
        <select name="id_equipement">
            <option value="">-- Aucun --</option>
            <?php foreach ($equipements as $eq): ?>
                <option value="<?= $eq['id_equipement'] ?>"><?= htmlspecialchars($eq['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Duree (minutes)</label>
        <input type="number" name="duree_minutes" min="1" required>

        <button type="submit">Enregistrer la seance</button>
    </form>

    <h1 style="margin-top:36px;">Seances recentes</h1>
    <table>
        <thead>
        <tr><th>Adherent</th><th>Salle</th><th>Activite</th><th>Equipement</th><th>Duree</th><th>Date</th></tr>
        </thead>
        <tbody>
        <?php foreach (array_slice($seances, 0, 30) as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['adherent_prenom'] . ' ' . $s['adherent_nom']) ?></td>
                <td><?= htmlspecialchars($s['salle_nom']) ?></td>
                <td><?= htmlspecialchars($s['activite_nom']) ?></td>
                <td><?= htmlspecialchars($s['equipement_nom'] ?? '-') ?></td>
                <td><?= (int)$s['duree_minutes'] ?> min</td>
                <td><?= htmlspecialchars($s['date_seance']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($seances)): ?>
            <tr><td colspan="6">Aucune seance enregistree.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
