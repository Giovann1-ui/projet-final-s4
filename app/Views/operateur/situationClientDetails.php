<?= $this->extend('Layout/app') ?>
<?= $this->section('title') ?>Détail client<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Détail du client<?= $this->endSection() ?>
<?= $this->section('breadcrumb') ?><a href="<?= base_url('operateur/situation-clients') ?>">Situation des clients</a> <i class="fas fa-chevron-right" style="font-size:.6rem"></i> <?= esc($client['num_tel'] ?? 'Client') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1rem">
	<div>
		<h2 style="margin:0 0 .35rem 0;font-family:'Playfair Display',serif"><?= esc($client['num_tel'] ?? 'Client inconnu') ?></h2>
		<div style="color:var(--muted)">ID client: <?= (int) ($clientId ?? 0) ?></div>
	</div>
	<a href="<?= base_url('operateur/situation-clients') ?>" class="btn-forest" style="padding:8px 14px;font-size:.82rem;text-decoration:none">
		<i class="fas fa-arrow-left"></i> Retour à la liste
	</a>
</div>

<div class="metrics">
	<div class="metric">
		<div class="metric-top"><div class="metric-icon mi-green"><i class="fas fa-coins"></i></div></div>
		<div class="metric-val"><?= number_format((float) ($soldeTotal ?? 0), 2, ',', ' ') ?></div>
		<div class="metric-label">Solde total</div>
	</div>
	<div class="metric">
		<div class="metric-top"><div class="metric-icon mi-amber"><i class="fas fa-exchange-alt"></i></div></div>
		<div class="metric-val"><?= count($transactions ?? []) ?></div>
		<div class="metric-label">Transactions</div>
	</div>
</div>

<div class="data-card" style="margin-bottom:1rem">
	<div class="data-card-head">
		<h3>Solde par type d'opération</h3>
	</div>
	<?php if (empty($soldeParTypeOperation)): ?>
	<div class="empty">
		<i class="fas fa-coins"></i>
		<p>Aucune opération trouvée pour ce client.</p>
	</div>
	<?php else: ?>
	<table class="tbl">
		<thead>
			<tr>
				<th>Type</th>
				<th>Entrant</th>
				<th>Sortant</th>
				<th>Solde</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($soldeParTypeOperation as $ligne): ?>
			<tr>
				<td class="td-name"><?= esc($ligne['libelle']) ?></td>
				<td class="td-mono"><?= number_format((float) $ligne['entrant'], 2, ',', ' ') ?></td>
				<td class="td-mono"><?= number_format((float) $ligne['sortant'], 2, ',', ' ') ?></td>
				<td class="td-mono" style="font-weight:500;color:<?= (float) $ligne['solde'] < 0 ? 'var(--danger)' : 'var(--success)' ?>"><?= number_format((float) $ligne['solde'], 2, ',', ' ') ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</div>

<div class="data-card">
	<div class="data-card-head">
		<h3>Transactions du client</h3>
	</div>

	<?php if (empty($transactions)): ?>
	<div class="empty">
		<i class="fas fa-receipt"></i>
		<p>Aucune transaction disponible.</p>
	</div>
	<?php else: ?>
	<table class="tbl">
		<thead>
			<tr>
				<th>Date</th>
				<th>Type</th>
				<th>Source</th>
				<th>Destination</th>
				<th>Montant brut</th>
				<th>Frais</th>
				<th>Entrant</th>
				<th>Sortant</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($transactions as $transaction): ?>
			<?php
				$isIncoming = (int) ($transaction['client_dest'] ?? 0) === (int) ($clientId ?? 0);
				$direction = $isIncoming ? 'Entrée' : 'Sortie';
			?>
			<tr>
				<td class="td-mono"><?= !empty($transaction['date']) ? date('d/m/Y H:i', strtotime($transaction['date'])) : '—' ?></td>
				<td><span class="type-badge t-sans-solde"><?= esc($transaction['type_libelle'] ?? '—') ?></span> <span class="td-muted" style="font-size:.75rem"><?= $direction ?></span></td>
				<td class="td-mono"><?= $transaction['client_source'] !== null ? (int) $transaction['client_source'] : '—' ?></td>
				<td class="td-mono"><?= $transaction['client_dest'] !== null ? (int) $transaction['client_dest'] : '—' ?></td>
				<td class="td-mono"><?= number_format((float) ($transaction['montant_brut'] ?? 0), 2, ',', ' ') ?></td>
				<td class="td-mono"><?= number_format((float) ($transaction['frais'] ?? 0), 2, ',', ' ') ?></td>
				<td class="td-mono" style="color:<?= $isIncoming ? 'var(--success)' : 'var(--muted)' ?>"><?= number_format((float) ($transaction['montant_entrant'] ?? 0), 2, ',', ' ') ?></td>
				<td class="td-mono" style="color:<?= !$isIncoming ? 'var(--danger)' : 'var(--muted)' ?>"><?= number_format((float) ($transaction['montant_sortant'] ?? 0), 2, ',', ' ') ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
<?= $this->endSection() ?>
