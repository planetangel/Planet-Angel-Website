<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<ul class="articlelist">
	<?php foreach ($this->items as $item) : ?>
		<li><a href="<?php echo $item->link; ?>"><?php echo $this->escape($item->title); ?></a>

			<?php if ($this->params->get('show_date')) : ?>
				<span class="articlelistdatecreated">
					<?php echo $item->created; ?>
				</span>
			<?php endif; ?>

			<?php if ($this->params->get('show_author')) : ?>
				<span class="articlelistauthor">
					<?php echo $this->escape($item->created_by_alias) ? $this->escape($item->created_by_alias) : $this->escape($item->author); ?>
				</span>
			<?php endif; ?>

			<?php if ($this->params->get('show_hits')) : ?>
				<span class="articlelisthits">
					<?php echo $this->escape($item->hits) ? $this->escape($item->hits) : '-'; ?>
				</span>
			<?php endif; ?>

		</li>
	<?php endforeach; ?>
</ul>