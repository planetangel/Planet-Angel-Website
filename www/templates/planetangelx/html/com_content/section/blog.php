<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$cparams =& JComponentHelper::getParams('com_media');
$pageClassSuffix = $this->escape($this->params->get('pageclass_sfx'));
?>

<?php if ($this->params->get('show_page_title', 1)) : ?>
	<h1 class="componentheading<?php echo $pageClassSuffix; ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</h1>
<?php endif; ?>

<div class="blog<?php echo $pageClassSuffix; ?>">

	<?php if ($this->params->def('show_description', 1)) { ?>
		<div class="blogdescription">
			<?php if ($this->params->get('show_description') && $this->category->description) { ?>
				<?php echo $this->category->description; ?>
			<?php }; ?>
		</div>
	<?php }; ?>

	<?php

	$i = $this->pagination->limitstart;
	$rowcount = $this->params->def('num_leading_articles', 1);

	if ($rowcount) { ?>
		<div class="blogleadingarticles<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
			<?php for ($y = 0; $y < $rowcount && $i < $this->total; $y++, $i++) { ?>
				<div class="blogarticle<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
					<?php
					  $this->item =& $this->getItem($i, $this->params);
					  echo $this->loadTemplate('item');
					?>
					<div class="separator<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"></div>
				</div>
			<?php }; ?>
		</div>
	<?php };

	$introcount = (int)$this->params->def('num_intro_articles', 4);

	if ($introcount) { ?>

		<div class="blogintroarticles"> <?php

			$colcount = (int)$this->params->def('num_columns', 2);
			if ($colcount == 0) {
				$colcount = 1;
			};

			$rowcount = (int) $introcount / $colcount;
			$ii = 0;

			for ($x = 0; $x < $colcount && $i < $this->total; $x++) { ?>

				<?php if ($colcount > 1) { ?>
				<div class="article_column column<?php echo $x + 1; ?> cols<?php echo $colcount; ?>" >
				<?php }; ?>
					<?php for ($y = 0; $y < $rowcount && $ii < $introcount && $i < $this->total; $y++) { ?>
						<div class="blogarticle<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
							<?php
								$this->item =& $this->getItem($i, $this->params);
								echo $this->loadTemplate('item');
								$i++;
								$ii++;
							?>
							<div class="separator<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"></div>
						</div>
					<?php }; ?>
				<?php if ($colcount > 1) { ?>
				</div>
				<?php }; ?>
			<?php }; ?>

		</div>

	<?php };

	$numlinks = (int)$this->params->def('num_links', 4);

	if ($numlinks && $i < $this->total) { ?>
		<div class="bloglinks<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
			<?php
			  $this->links = array_slice($this->items, $i - $this->pagination->limitstart, $i - $this->pagination->limitstart + $numlinks);
			  echo $this->loadTemplate('links');
			?>
		</div>
	<?php }; ?>

	<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) {

		if( $this->pagination->get('pages.total') > 1 ) { ?>
			<div class="blogpagination<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</div>
		<?php };

		if ($this->params->def('show_pagination_results', 1)) { ?>
			<div class="blogpaginationresults<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php }; ?>

	<?php }; ?>

</div>