<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$canEdit	     = ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own'));
$pageClassSuffix = $this->escape($this->params->get('pageclass_sfx'));

// Overall page header
if ($this->params->get('show_page_title', 1) && $this->params->get('page_title') != $this->article->title) { ?>
	<h1 class="componentheading<?php echo $pageClassSuffix; ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</h1>
<?php }; ?>

<div class="contentpane<?php echo $pageClassSuffix; ?>">

	<?php if ($canEdit || $this->params->get('show_title') || $this->params->get('show_pdf_icon') || $this->params->get('show_print_icon') || $this->params->get('show_email_icon')) : ?>
		<?php if ($this->params->get('show_title')) : ?>
		<h2 class="contentheading<?php echo $pageClassSuffix; ?>">
			<?php if ($this->params->get('link_titles') && $this->article->readmore_link != '') : ?>
			<a href="<?php echo $this->article->readmore_link; ?>" class="contentpagetitle<?php echo $pageClassSuffix; ?>">
				<?php echo $this->escape($this->article->title); ?></a>
			<?php else : ?>
				<?php echo $this->escape($this->article->title); ?>
			<?php endif; ?>
		</h2>
		<?php endif; ?>
		<?php if (!$this->print) : ?>
			<?php if ($this->params->get('show_pdf_icon')) : ?>
			<span class="buttonheading">
			<?php echo JHTML::_('icon.pdf',  $this->article, $this->params, $this->access); ?>
			</span>
			<?php endif; ?>

			<?php if ( $this->params->get( 'show_print_icon' )) : ?>
			<span class="buttonheading">
			<?php echo JHTML::_('icon.print_popup',  $this->article, $this->params, $this->access); ?>
			</span>
			<?php endif; ?>

			<?php if ($this->params->get('show_email_icon')) : ?>
			<span class="buttonheading">
			<?php echo JHTML::_('icon.email',  $this->article, $this->params, $this->access); ?>
			</span>
			<?php endif; ?>
			<?php if ($canEdit) : ?>
			<span class="buttonheading">
				<?php echo JHTML::_('icon.edit', $this->article, $this->params, $this->access); ?>
			</span>
			<?php endif; ?>
		<?php else : ?>
			<span class="buttonheading">
			<?php echo JHTML::_('icon.print_screen',  $this->article, $this->params, $this->access); ?>
			</span>
		<?php endif; ?>
	<?php endif; ?>

	<?php  if (!$this->params->get('show_intro')) :
		echo $this->article->event->afterDisplayTitle;
	endif; ?>

	<?php echo $this->article->event->beforeDisplayContent; ?>

	<?php if (($this->params->get('show_section') && $this->article->sectionid) || ($this->params->get('show_category') && $this->article->catid)) : ?>
		<?php if ($this->params->get('show_section') && $this->article->sectionid && isset($this->article->section)) : ?>
		<div class="linksection">
			<?php if ($this->params->get('link_section')) : ?>
				<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->article->sectionid)).'">'; ?>
			<?php endif; ?>
			<?php echo $this->escape($this->article->section); ?>
			<?php if ($this->params->get('link_section')) : ?>
				<?php echo '</a>'; ?>
			<?php endif; ?>
				<?php if ($this->params->get('show_category')) : ?>
				<?php echo ' - '; ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php if ($this->params->get('show_category') && $this->article->catid) : ?>
		<div class="linkcategory">
			<?php if ($this->params->get('link_category')) : ?>
				<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->article->catslug, $this->article->sectionid)).'">'; ?>
			<?php endif; ?>
			<?php echo $this->escape($this->article->category); ?>
			<?php if ($this->params->get('link_category')) : ?>
				<?php echo '</a>'; ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	<?php endif; ?>
	<?php if (($this->params->get('show_author')) && ($this->article->author != "")) : ?>
		<div class="author">
			<?php JText::printf( 'Written by', ($this->escape($this->article->created_by_alias) ? $this->escape($this->article->created_by_alias) : $this->escape($this->article->author)) ); ?>
		</div>
	<?php endif; ?>
	<?php if ($this->params->get('show_create_date')) : ?>
		<div class="createdate">
			<?php echo JHTML::_('date', $this->article->created, JText::_('DATE_FORMAT_LC2')) ?>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_url') && $this->article->urls) : ?>
		<div class="articleurls">
			<a href="http://<?php echo $this->article->urls ; ?>" target="_blank">
			<?php echo $this->escape($this->article->urls); ?></a>
		</div>
	<?php endif; ?>

	<?php if (isset ($this->article->toc)) : ?>
		<?php echo $this->article->toc; ?>
	<?php endif; ?>

	<div class="articlecontent">
		<?php echo $this->article->text; ?>
	</div>


	<?php if ( intval($this->article->modified) !=0 && $this->params->get('show_modify_date')) : ?>
		<div class="modifydate">
			<?php echo JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->article->modified, JText::_('DATE_FORMAT_LC2'))); ?>
		</div>
	<?php endif; ?>

</div>

<?php echo $this->article->event->afterDisplayContent; ?>
