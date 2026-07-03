<?php /** @var \App\Entities\Abonnement[] $abonnements */ ?>
<?php require __DIR__ . '/../partials/nav.php'; ?>

<div class="container">
    <h1>Abonnements</h1>
    <p class="subtitle">Historique des abonnements de tous les adherents</p>

    <?php if ($succes): ?><div class="alert alert-success"><?= htmlspecialchars($succes) ?></div><?php endif; ?>

    <div class="top-actions">
        <span><?= count($abonnements) ?> abonnement(s)</span>
        <a class="btn-primary" href="index.php?page=abonnements&action=create">+ Nouvel abonnement</a>
    </div>

    <table>
        <thead>
        <tr><th>Adherent</th><th>Type</th><th>Debut</th><th>Fin</th><th>Statut</th></tr>
        </thead>
        <tbody>
        <?php foreach ($abonnements as $ab): ?>
            <?php
                $nomAdherent = '#' . $ab->getIdAdherent();
                foreach ($adherents as $a) {
                    if ($a->getIdAdherent() === $ab->getIdAdherent()) { $nomAdherent = $a->getNomComplet(); break; }
                }
                $badgeClass = 'badge-' . $ab->getStatut();
            ?>
            <tr>
                <td><?= htmlspecialchars($nomAdherent) ?></td>
                <td><?= htmlspecialchars(ucfirst($ab->getType())) ?></td>
                <td><?= htmlspecialchars($ab->getDateDebut()) ?></td>
                <td><?= htmlspecialchars($ab->getDateFin()) ?></td>
                <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($ab->getStatut()) ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($abonnements)): ?>
            <tr><td colspan="5">Aucun abonnement pour le moment.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
