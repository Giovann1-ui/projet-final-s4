<?= $this->extend('Layout/app') ?>
<?= $this->section('title') ?>Situation des clients<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Situation des comptes clients<?= $this->endSection() ?>
<?= $this->section('breadcrumb') ?><a href="<?= base_url('operateur') ?>">Opérateur</a> <i class="fas fa-chevron-right" style="font-size:.6rem"></i> Clients<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="metrics">
	<div class="metric">
		<div class="metric-top"><div class="metric-icon mi-green"><i class="fas fa-users"></i></div></div>
		<div class="metric-val"><?= count($listeClients ?? []) ?></div>
		<div class="metric-label">Clients suivis</div>
	</div>
	<div class="metric">
		<div class="metric-top"><div class="metric-icon mi-amber"><i class="fas fa-wallet"></i></div></div>
		<div class="metric-val"><?= number_format(array_sum(array_map(static fn ($client) => (float) ($client['solde'] ?? 0), $listeClients ?? [])), 2, ',', ' ') ?></div>
		<div class="metric-label">Solde total cumulé</div>
	</div>
</div>

<div class="data-card">
	<div class="data-card-head">
		<h3>Liste des clients</h3>
	</div>

	<?php if (empty($listeClients)): ?>
	<div class="empty">
		<i class="fas fa-users-slash"></i>
		<p>Aucun client disponible.</p>
	</div>
	<?php else: ?>
	<table class="tbl">
		<thead>
			<tr>
				<th>Numéro</th>
				<th>Date d'inscription</th>
				<th>Solde</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($listeClients as $client): ?>
			<tr>
				<td class="td-name"><?= esc($client['num_tel']) ?></td>
				<td class="td-mono"><?= !empty($client['date_inscription']) ? date('d/m/Y H:i', strtotime($client['date_inscription'])) : '—' ?></td>
				<td class="td-mono" style="font-weight:500;color:<?= (float) $client['solde'] < 0 ? 'var(--danger)' : 'var(--success)' ?>"><?= number_format((float) $client['solde'], 2, ',', ' ') ?></td>
				<td>
					<a href="<?= base_url('operateur/situation-clients/' . $client['id']) ?>" class="btn-forest" style="padding:6px 12px;font-size:.8rem;text-decoration:none">
						<i class="fas fa-eye"></i> Détails
					</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
<?= $this->endSection() ?>
