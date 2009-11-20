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

	<?php if ($this->params->def('show_description', 1)) : ?>
		<div class="blogdescription">
			<?php if ($this->params->get('show_description') && $this->category->description) : ?>
				<?php echo $this->category->description; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('num_leading_articles')) : ?>
		<div class="blogleadingarticles">
			<?php for ($i = $this->pagination->limitstart; $i < ($this->pagination->limitstart + $this->params->get('num_leading_articles')); $i++) : ?>
				<?php if ($i >= $this->total) : break; endif; ?>
				<div class="blogarticle">
					<?php
						$this->item =& $this->getItem($i, $this->params);
						echo $this->loadTemplate('item');
					?>
				</div>
			<?php endfor; ?>
		</div>
	<?php else : $i = $this->pagination->limitstart; endif; ?>

	<?php
	$startIntroArticles = $this->pagination->limitstart + $this->params->get('num_leading_articles');
	$numIntroArticles = $startIntroArticles + $this->params->get('num_intro_articles');
	if (($numIntroArticles != $startIntroArticles) && ($i < $this->total)) : ?>
		<div class="blogintroarticles">
			<?php for ($i = $startIntroArticles; $i <= $startIntroArticles + $numIntroArticles; $i++) : ?>
				<?php if ($i >= $this->total) : break; endif; ?>
				<div class="blogarticle">
					<?php
						$this->item =& $this->getItem($i, $this->params);
						echo $this->loadTemplate('item');
					?>
				</div>
			<?php endfor; ?>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('num_links') && ($i < $this->total)) : ?>
		<div class="bloglinks">
			<?php
				$this->links = array_splice($this->items, $i - $this->pagination->limitstart);
				echo $this->loadTemplate('links');
			?>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_pagination')) : ?>
		<div class="blogpagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_pagination_results')) : ?>
		<div class="blogpaginationresults">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</div>
	<?php endif; ?>

</div>
