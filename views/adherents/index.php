<?php /** @var \App\Entities\Adherent[] $adherents */ ?>
<?php require __DIR__ . '/../partials/nav.php'; ?>

<div class="container">
    <h1>Adherents</h1>
    <p class="subtitle">Liste des adherents du reseau FitConnect</p>

    <?php if ($succes): ?><div class="alert alert-success"><?= htmlspecialchars($succes) ?></div><?php endif; ?>
    <?php if ($erreur): ?><div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

    <div class="top-actions">
        <span><?= count($adherents) ?> adherent(s)</span>
        <a class="btn-primary" href="index.php?page=adherents&action=create">+ Nouvel adherent</a>
    </div>

    <table>
        <thead>
        <tr>
            <th>Nom</th><th>Email</th><th>Telephone</th><th>Salle</th><th>Inscrit le</th><th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($adherents as $a): ?>
            <?php
                $salleNom = '#' . $a->getIdSalle();
                foreach ($salles as $s) {
                    if ((int)$s['id_salle'] === $a->getIdSalle()) { $salleNom = $s['nom']; break; }
                }
            ?>
            <tr>
                <td><?= htmlspecialchars($a->getNomComplet()) ?></td>
                <td><?= htmlspecialchars($a->getEmail()) ?></td>
                <td><?= htmlspecialchars($a->getTelephone() ?? '-') ?></td>
                <td><?= htmlspecialchars($salleNom) ?></td>
                <td><?= htmlspecialchars($a->getDateInscription()) ?></td>
                <td class="actions">
                    <a class="btn-danger" href="index.php?page=adherents&delete=<?= $a->getIdAdherent() ?>"
                       onclick="return confirm('Supprimer cet adherent ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($adherents)): ?>
            <tr><td colspan="6">Aucun adherent pour le moment.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
