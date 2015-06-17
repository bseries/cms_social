<?php

use lithium\g11n\Message;

$t = function($message, array $options = []) {
	return Message::translate($message, $options + ['scope' => 'cms_social', 'default' => $message]);
};

$this->set([
	'page' => [
		'type' => 'multiple',
		'object' => $t('social stream')
	]
]);

?>
<article
	class="use-rich-index"
	data-endpoint="<?= $this->url([
		'action' => 'index',
		'page' => '__PAGE__',
		'orderField' => '__ORDER_FIELD__',
		'orderDirection' => '__ORDER_DIRECTION__',
		'filter' => '__FILTER__'
	]) ?>"
>

	<div class="top-actions">
		<?= $this->html->link($t('refresh'), ['action' => 'poll'], ['class' => 'button add']) ?>
	</div>

	<?php if ($data->count()): ?>
		<table>
			<thead>
				<tr>
					<td data-sort="is-published" class="flag table-sort"><?= $t('publ.?') ?>
					<td><?= $t('Type') ?>
					<td data-sort="title" class="emphasize title table-sort"><?= $t('Title') . '/' . $t('Excerpt') ?>
					<td data-sort="published" class="date published table-sort"><?= $t('Pubdate') ?>
					<td data-sort="modified" class="date modified table-sort desc"><?= $t('Modified') ?>
					<td class="actions">
						<?= $this->form->field('search', [
							'type' => 'search',
							'label' => false,
							'placeholder' => $t('Filter'),
							'class' => 'table-search',
							'value' => $this->_request->filter
						]) ?>
			</thead>
			<tbody>
				<?php foreach ($data as $item): ?>
				<tr>
					<td class="flag"><i class="material-icons"><?= ($item->is_published ? 'done' : '') ?></i>
					<td><?= $item->type() ?>
					<td class="emphasize title"><?= $item->title ?: $item->excerpt ?>
					<td class="date published">
						<time datetime="<?= $this->date->format($item->published, 'w3c') ?>">
							<?= $this->date->format($item->published, 'date') ?>
						</time>
					<td class="date modified">
						<time datetime="<?= $this->date->format($item->modified, 'w3c') ?>">
							<?= $this->date->format($item->modified, 'date') ?>
						</time>
					<td class="actions">
						<?= $this->html->link($item->is_published ? $t('unpublish') : $t('publish'), ['id' => $item->id, 'action' => $item->is_published ? 'unpublish': 'publish', 'library' => 'cms_social'], ['class' => 'button']) ?>
				<?php endforeach ?>
			</tbody>
		</table>
	<?php else: ?>
		<div class="none-available"><?= $t('No items available, yet.') ?></div>
	<?php endif ?>

	<?=$this->view()->render(['element' => 'paging'], compact('paginator'), ['library' => 'base_core']) ?>
</article>