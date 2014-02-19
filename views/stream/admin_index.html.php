<?php

$dateFormatter = new IntlDateFormatter(
	'de_DE',
	IntlDateFormatter::SHORT,
	IntlDateFormatter::SHORT,
	'Europe/Berlin'
);

$timeElementForDate = function($value) use ($dateFormatter) {
	$date = DateTime::createFromFormat('Y-m-d H:i:s', $value, new DateTimeZone('UTC'));

	$standard = $date->format(DateTime::W3C);
	$display = $dateFormatter->format($date);

	return '<time datetime="' . $standard . '">' . $display . '</time>';
};

?>
<article class="view-<?= $this->_config['controller'] . '-' . $this->_config['template'] ?>">
	<h1 class="alpha"><?= $this->title($t('Social Stream')) ?></h1>

	<?php if ($data->count()): ?>
		<table>
			<thead>
				<tr>
					<td class="flag"><?= $t('publ.?') ?>
					<td><?= $t('Type') ?>
					<td class="emphasize"><?= $t('Title') . '/' . $t('Excerpt') ?>
					<td class="date published"><?= $t('Pubdate') ?>
					<td class="date created"><?= $t('Created') ?>
					<td>
			</thead>
			<tbody>
				<?php foreach ($data as $item): ?>
				<tr>
					<td class="flag"><?= ($item->is_published ? '✓' : '╳') ?>
					<td><?= $item->type() ?>
					<td class="emphasize"><?= $item->title ?: $item->excerpt ?>
					<td class="date published">
						<?php $date = DateTime::createFromFormat('Y-m-d H:i:s', $item->published) ?>
						<time datetime="<?= $date->format(DateTime::W3C) ?>"><?= $dateFormatter->format($date) ?></time>
					<td class="date created">
						<?php $date = DateTime::createFromFormat('Y-m-d H:i:s', $item->created) ?>
						<time datetime="<?= $date->format(DateTime::W3C) ?>"><?= $dateFormatter->format($date) ?></time>
					<td>
						<nav class="actions">
							<?= $this->html->link($item->is_published ? $t('unpublish') : $t('publish'), ['id' => $item->id, 'action' => $item->is_published ? 'unpublish': 'publish', 'library' => 'cms_social'], ['class' => 'button']) ?>
						</nav>
				<?php endforeach ?>
			</tbody>
		</table>
	<?php else: ?>
		<div class="none-available"><?= $t('No items available, yet.') ?></div>
	<?php endif ?>
</article>