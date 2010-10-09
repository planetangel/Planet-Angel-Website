<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
error_log(print_r($this, true));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/system.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/planetangelx/css/template_css.css" type="text/css" />
<?php
	$paSect = "home";
	if ( $this->countModules( 'newsmenu' ) ) {
		$paSect = "news";
	} else if ( $this->countModules( 'aboutmenu' ) ) {
		$paSect = "about";
	} else if ( $this->countModules( 'communitymenu' ) ) {
		$paSect = "community";
	} else if ( $this->countModules( 'eventsmenu' ) ) {
		$paSect = "events";
	} else if ( $this->countModules( 'shopmenu' ) ) {
		$paSect = "shop";
	} else if ( $this->countModules( 'contactmenu' ) ) {
		$paSect = "contact";
	}

	if ($paSect != "home") {
		?><link href="<?php echo $this->baseurl ?>/templates/planetangelx/css/template_css_<?php echo $paSect; ?>.css" rel="stylesheet" type="text/css"/><?php
	}

?>
<script language="javascript" src="/templates/planetangelx/js/countdown_clock.js"></script>

<?php // BEGIN GREYBOX ?>
<script type="text/javascript">
    var GB_ROOT_DIR = "<?php echo $this->base ?>lib/greybox/";
</script>
<script type="text/javascript" src="/lib/greybox/AJS.js"></script>
<script type="text/javascript" src="/lib/greybox/AJS_fx.js"></script>
<script type="text/javascript" src="/lib/greybox/gb_scripts.js"></script>
<link href="/lib/greybox/gb_styles.css" rel="stylesheet" type="text/css" />
<?php // END GREYBOX ?>

</head>
<body>
	<div id="wrapper" align="center">
		<div id="wholepage" align="left">

			<!-- Header -->
			<?php if ( $this->countModules ('searchbox') ) { ?>
				<div id="modules_searchbox">
					<jdoc:include type="modules" name="searchbox" style="xhtml" />
				</div>
			<?php } ?>
			<div id="topsection">
				<div id="header">
					<h1><a href="/">Planet Angel - Live an Ordinary Life in an Extraordinary Way</a></h1>
					<span id="angelouter">
						<span id="theangel">
							<a href="nextparty/">The Angel</a>
						</span>
					</span>
				</div>
				<div id="eyecatchers">
					<?php if ( $this->countModules( 'eyecatcher' ) ) { ?>
						<div id="modules_eyecatcher">
							<jdoc:include type="modules" name="eyecatcher" style="xhtml" />
						</div>
					<?php } ?>
					<?php if ( $this->countModules( 'floaters' ) ) { ?>
						<div id="modules_floaters">
							<jdoc:include type="modules" name="floaters" style="rounded" />
						</div>
					<?php } ?>
				</div>
				<?php if ( $this->countModules ('specialnav') ) { ?>
					<div id="modules_specialnav">
						<jdoc:include type="modules" name="specialnav" style="xhtml" />
					</div>
				<?php } ?>
			</div>
			<!-- End of header -->

			<!-- Main menu -->
			<div id="menuouter">
				<div id="menuinner">
					<ul id="menu">
						<li id="mnews<?php if ($paSect == "news") { echo 'sel'; } ?>"><a href="news">News</a>
							<?php if ( $this->countModules( 'newspopup' ) ) {
								?><jdoc:include type="modules" name="newspopup" style="xhtml" /><?php
							} ?>
						</li>
						<li id="mabout<?php if ($paSect == "about") { echo 'sel'; } ?>"><a href="about">About</a>
							<?php if ( $this->countModules( 'aboutpopup' ) ) {
								?><jdoc:include type="modules" name="aboutpopup" style="xhtml" /><?php
							} ?>
						</li>
						<li id="mcommunity<?php if ($paSect == "community") { echo 'sel'; } ?>"><a href="community">Community</a>
							<?php if ( $this->countModules( 'commpopup' ) ) {
								?><jdoc:include type="modules" name="commpopup" style="xhtml" /><?php
							} ?>
						</li>
						<li id="mevents<?php if ( $paSect == "events") { echo 'sel'; } ?>"><a href="events">Events</a>
							<?php if ( $this->countModules( 'eventpopup' ) ) {
								?><jdoc:include type="modules" name="eventpopup" style="xhtml" /><?php
							} ?>
						</li>
						<li id="mshop<?php if ( $paSect == "shop") { echo 'sel'; } ?>"><a href="the-shop">Shop</a>
							<?php if ( $this->countModules( 'shoppopup' ) ) {
								?><jdoc:include type="modules" name="shoppopup" style="xhtml" /><?php
							} ?>
						</li>
						<li id="mcontact<?php if ( $paSect == "contact") { echo 'sel'; } ?>"><a href="contact">Contact</a>
							<?php if ( $this->countModules( 'contpopup' ) ) {
								?><jdoc:include type="modules" name="contpopup" style="xhtml" /><?php
							} ?>
						</li>
					</ul>
				</div>
			</div>
			<!-- End of main menu-->

			<!-- Left hand side menus -->
			<div id="mainmodules">
				<?php if ( $this->countModules( 'homemenu' ) ) { ?>
					<div id="modules_sect_tit">
						<jdoc:include type="modules" name="homemenu" style="rounded" />
					</div>
				<?php } ?>
				<?php if ( $this->countModules( 'newsmenu' ) ) { ?>
					<div id="modules_sect_tit">
						<jdoc:include type="modules" name="newsmenu" style="rounded" />
					</div>
				<?php } ?>
				<?php if ( $this->countModules( 'aboutmenu' ) ) { ?>
					<div id="modules_sect_tit">
						<jdoc:include type="modules" name="aboutmenu" style="rounded" />
					</div>
				<?php } ?>
				<?php if ( $this->countModules( 'communitymenu' ) ) { ?>
					<div id="modules_sect_tit">
						<jdoc:include type="modules" name="communitymenu" style="rounded" />
					</div>
				<?php } ?>
				<?php if ( $this->countModules( 'eventsmenu' ) ) { ?>
					<div id="modules_sect_tit">
						<jdoc:include type="modules" name="eventsmenu" style="rounded" />
					</div>
				<?php } ?>
				<?php if ( $this->countModules( 'contactmenu' ) ) { ?>
					<div id="modules_sect_tit">
						<jdoc:include type="modules" name="contactmenu" style="rounded" />
					</div>
				<?php } ?>
				<?php if ( $this->countModules( 'shopmenu' ) ) { ?>
					<div id="modules_sect_tit">
						<jdoc:include type="modules" name="shopmenu" style="rounded" />
					</div>
				<?php } ?>
				<?php if ( $this->countModules ('mainmenu') ) { ?>
					<div id="modules_mainmenu">
						<jdoc:include type="modules" name="mainmenu" style="rounded" />
					</div>
				<?php } ?>

				<?php if ( $this->countModules( 'context' ) ) { ?>
					<div id="modules_context">
						<jdoc:include type="modules" name="context" style="rounded" />
					</div>
				<?php } ?>
			</div>
			<!-- End of left hand side menus -->

			<!-- Start of main content area -->
			<div id="center">

				<div id="pathway" class="pathway">
					<jdoc:include type="modules" name="breadcrumb" />&nbsp;
				</div>

				<?php
				$featuresCount = $this->countModules('features');
				if ($featuresCount) {
					echo "<table id=\"main_body_hack_table\" cellpadding=\"0\" cellspacing=\"0\"><tr><td id=\"main_body_hack_main\">";
				} else {
					echo "<div id=\"main_body_outer_wide\">";
				} ?>

					<div id="main_body">

						<?php if ( $this->countModules( 'emergency' ) ) { ?>
							<div id="modules_emergency">
								<jdoc:include type="modules" name="emergency" style="rounded" />
							</div>
						<?php } ?>

						<!-- ********************** MAIN PAGE BODY ********************************** -->

						<jdoc:include type="component" />

						<!-- ********************** MAIN PAGE BODY END ****************************** -->


						<?php if ( $this->countModules( 'footnotes' ) ) { ?>
							<div id="modules_footnotes">
								<jdoc:include type="modules" name="footnotes" style="rounded" />
							</div>
							<div id="footnotes_clear" style="clear:both;"></div>
						<?php } ?>

						<?php if ( $this->countModules( 'footnotes-left' ) ) { ?>
							<div id="modules_footnotes-left">
								<jdoc:include type="modules" name="footnotes-left" style="rounded" />
							</div>
						<?php } ?>

						<?php if ( $this->countModules( 'footnotes-right' ) ) { ?>
							<div id="modules_footnotes-right">
								<jdoc:include type="modules" name="footnotes-right" style="rounded" />
							</div>
						<?php } ?>

						<div id="footnotes_clear" style="clear:both;"></div>

					</div>

				<?php
				if ($featuresCount) {
					echo "</td><td id=\"main_body_hack_features\">";
					?><jdoc:include type="modules" name="features" style="rounded" /><?php
					echo "</td></tr></table>";
				} else {
					echo "</div>";
				} ?>

				<div id="bottombar"></div>

				<div id="footer">
					<div style="width: 100%; height: 80px; position: relative;">
						<div style="position: absolute; right: 8px;">
							<a href="http://www.ethical-junction.org/" title="We are members of Ethical Junction 2009" target="_blank">
								<img src="http://www.ethical-junction.org/images/members/EJ-Membership-2009-1.png" style="border: medium none ;" alt="Ethical Junction Member 2009" height="60" width="120">
							</a>
						</div>
					<div>Planet Angel is a not-for-profit company, limited by guarantee.</div>
					<div>&copy; Planet Angel 1999 - 2009</div>
					<div>All Rights Reserved. <a href="/listc/credits/">Click here</a> for a full list of credits.</div>
				</div>


			</div>
			<!-- End of main content area -->
		</div>
	</div>
<jdoc:include type="modules" name="debug" />
</body>
</html>
