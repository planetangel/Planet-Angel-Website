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


<?php if ($this->params->get('show_description') && $this->section->description) : ?>
	<div class="sectiondescription<?php echo $pageClassSuffix; ?>">
		<?php echo $this->section->description; ?>
	</div>
<?php endif; ?>

<?php if ($this->params->get('show_categories', 1)) : ?>
	<ul class="sectioncategorylist<?php echo $pageClassSuffix; ?>">
		<?php foreach ($this->categories as $category) : ?>
			<?php if (!$this->params->get('show_empty_categories') && !$category->numitems) continue; ?>
			<li>

				<div class="sectioncategory">
					<a class="category" href="<?php echo $category->link; ?>"><?php echo $this->escape($category->title);?></a>

					<?php if ($this->params->get('show_cat_num_articles')) : ?>
						<span class="numarticles">
							( <?php if ($category->numitems==1) {
							echo $category->numitems ." ". JText::_( 'item' );}
							else {
							echo $category->numitems ." ". JText::_( 'items' );} ?> )
						</span>
					<?php endif; ?>
				</div>

				<?php if ($this->params->def('show_category_description', 1) && $category->description) : ?>
					<div class="sectioncategorydescription">
						<?php echo $category->description; ?>
					</div>
				<?php endif; ?>

			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

