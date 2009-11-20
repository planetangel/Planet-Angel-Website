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

<?php if ($this->params->def('show_description', 1)) : ?>
	<div class="blogdescription">
		<?php if ($this->params->get('show_description') && $this->category->description) : ?>
			<?php echo $this->category->description; ?>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php
	$this->items =& $this->getItems();
	echo $this->loadTemplate('items');
?>

<?php if ($this->access->canEdit || $this->access->canEditOwn) :
		echo JHTML::_('icon.create', $this->category  , $this->params, $this->access);
endif; ?>
