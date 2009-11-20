<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php $canEdit   = ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own')); ?>
<?php if ($this->item->state == 0) : ?>
<div class="system-unpublished">
<?php endif; ?>

<div class="contentpane<?php echo $pageClassSuffix; ?>">

	<?php if ($canEdit || $this->params->get('show_title') || $this->params->get('show_pdf_icon') || $this->params->get('show_print_icon') || $this->params->get('show_email_icon')) : ?>
		<?php if ($this->params->get('show_title')) : ?>
		<h2 class="contentheading<?php echo $pageClassSuffix; ?>">
			<?php if ($this->params->get('link_titles') && $this->item->readmore_link != '') : ?>
			<a href="<?php echo $this->item->readmore_link; ?>" class="contentpagetitle<?php echo $pageClassSuffix; ?>">
				<?php echo $this->escape($this->item->title); ?></a>
			<?php else : ?>
				<?php echo $this->escape($this->item->title); ?>
			<?php endif; ?>
		</h2>
		<?php endif; ?>
		<?php if (!$this->print) : ?>
			<?php if ($this->params->get('show_pdf_icon')) : ?>
			<span class="buttonheading">
			<?php echo JHTML::_('icon.pdf',  $this->item, $this->params, $this->access); ?>
			</span>
			<?php endif; ?>

			<?php if ( $this->params->get( 'show_print_icon' )) : ?>
			<span class="buttonheading">
			<?php echo JHTML::_('icon.print_popup',  $this->item, $this->params, $this->access); ?>
			</span>
			<?php endif; ?>

			<?php if ($this->params->get('show_email_icon')) : ?>
			<span class="buttonheading">
			<?php echo JHTML::_('icon.email',  $this->item, $this->params, $this->access); ?>
			</span>
			<?php endif; ?>
			<?php if ($canEdit) : ?>
			<span class="buttonheading">
				<?php echo JHTML::_('icon.edit', $this->item, $this->params, $this->access); ?>
			</span>
			<?php endif; ?>
		<?php else : ?>
			<span class="buttonheading">
			<?php echo JHTML::_('icon.print_screen',  $this->item, $this->params, $this->access); ?>
			</span>
		<?php endif; ?>
	<?php endif; ?>

	<?php  if (!$this->params->get('show_intro')) :
		echo $this->item->event->afterDisplayTitle;
	endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if (($this->params->get('show_section') && $this->item->sectionid) || ($this->params->get('show_category') && $this->item->catid)) : ?>
		<?php if ($this->params->get('show_section') && $this->item->sectionid && isset($this->item->section)) : ?>
		<div class="linksection">
			<?php if ($this->params->get('link_section')) : ?>
				<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->item->sectionid)).'">'; ?>
			<?php endif; ?>
			<?php echo $this->escape($this->item->section); ?>
			<?php if ($this->params->get('link_section')) : ?>
				<?php echo '</a>'; ?>
			<?php endif; ?>
				<?php if ($this->params->get('show_category')) : ?>
				<?php echo ' - '; ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php if ($this->params->get('show_category') && $this->item->catid) : ?>
		<div class="linkcategory">
			<?php if ($this->params->get('link_category')) : ?>
				<?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug, $this->item->sectionid)).'">'; ?>
			<?php endif; ?>
			<?php echo $this->escape($this->item->category); ?>
			<?php if ($this->params->get('link_category')) : ?>
				<?php echo '</a>'; ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	<?php endif; ?>
	<?php if (($this->params->get('show_author')) && ($this->item->author != "")) : ?>
		<div class="author">
			<?php JText::printf( 'Written by', ($this->escape($this->item->created_by_alias) ? $this->escape($this->item->created_by_alias) : $this->escape($this->item->author)) ); ?>
		</div>
	<?php endif; ?>
	<?php if ($this->params->get('show_create_date')) : ?>
		<div class="createdate">
			<?php echo JHTML::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2')) ?>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_url') && $this->item->urls) : ?>
		<div class="articleurls">
			<a href="http://<?php echo $this->item->urls ; ?>" target="_blank">
			<?php echo $this->escape($this->item->urls); ?></a>
		</div>
	<?php endif; ?>

	<?php if (isset ($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
	<?php endif; ?>

	<div class="articlecontent">
		<?php echo $this->item->text; ?>
	</div>

	<?php if ( intval($this->item->modified) !=0 && $this->params->get('show_modify_date')) : ?>
		<div class="modifydate">
			<?php echo JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
		</div>
	<?php endif; ?>

	<?php if ($this->item->params->get('show_readmore') && $this->item->readmore) : ?>
		<div class="readmore">
			<a href="<?php echo $this->item->readmore_link; ?>" class="readon<?php echo $this->escape($this->item->params->get('pageclass_sfx')); ?>">
				<?php if ($this->item->readmore_register) :
					echo JText::_('Register to read more...');
				elseif ($readmore = $this->item->params->get('readmore')) :
					echo $readmore;
				else :
					echo JText::sprintf('Read more...');
				endif; ?></a>
		</div>
	<?php endif; ?>
</div>
<?php if ($this->item->state == 0) : ?>
</div>
<?php endif; ?>
<?php echo $this->item->event->afterDisplayContent; ?>
