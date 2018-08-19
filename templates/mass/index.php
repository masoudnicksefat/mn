<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var JDocumentHtml $this */

$app  = JFactory::getApplication();
$user = JFactory::getUser();

// Output as HTML5
$this->setHtml5(true);

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

if ($task === 'edit' || $layout === 'form')
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('jquery.framework');
//JHtml::_('bootstrap.framework');

// Add template js
JHtml::_('script', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('version' => 'auto', 'relative' => true));


// Add html5 shiv
//JHtml::_('script', 'jui/html5.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

// Add Stylesheets
JHtml::_('stylesheet', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'mass.css', array('version' => 'auto', 'relative' => true));

// Use of Google Font
/*
if ($this->params->get('googleFont'))
{
	JHtml::_('stylesheet', '//fonts.googleapis.com/css?family=' . $this->params->get('googleFontName'));
	$this->addStyleDeclaration("
	h1, h2, h3, h4, h5, h6, .site-title {
		font-family: '" . str_replace('+', ' ', $this->params->get('googleFontName')) . "', sans-serif;
	}");
}
*/
// Template color
if ($this->params->get('templateColor'))
{
	$this->addStyleDeclaration('
	.navbar-inverse {
	  background-color:' . $this->params->get('templateColor') . ';
	}
	#mm-skills , #mm-portfolio , #mm-testimonial {
		border-top: 3px solid ' . $this->params->get('templateColor') . ';
		
	}
	.nav li a:hover {
		color: ' . $this->params->get('templateColor') . ';
	}
	a {
		color: ' . $this->params->get('templateColor') . ';
	}
	#top-skills .moduletable h3 ,
	#portfolio .moduletable h3 ,
	#mm-testimonial  .moduletable h3 {
		background-color: ' . $this->params->get('templateColor') . ';
	}
	#top-skills .moduletable h3::after {
		border-color: ' . $this->params->get('templateColor') . ' ' . $this->params->get('templateColor') . ' transparent transparent;
	}
	#top-skills .moduletable h3::before {
		border-color: ' . $this->params->get('templateColor') . ' transparent transparent ' . $this->params->get('templateColor') . ';
	}
	#mm-portfolio .moduletable h3::after {
		border-color: ' . $this->params->get('templateColor') . ' ' . $this->params->get('templateColor') . ' transparent transparent;
	}
	#portfolio .moduletable h3::before {
		border-color: ' . $this->params->get('templateColor') . ' transparent transparent ' . $this->params->get('templateColor') . ';
	}
	#mm-testimonial .moduletable h3::after {
		border-color: ' . $this->params->get('templateColor') . ' ' . $this->params->get('templateColor') . ' transparent transparent;
	}
	#mm-testimonial .moduletable h3::before {
		border-color: ' . $this->params->get('templateColor') . ' transparent transparent ' . $this->params->get('templateColor') . ';
	}
	footer {
		background-color: ' . $this->params->get('templateColor') . ';
		color: ' . $this->params->get('templateBackgroundColor') . ';
	}
	#contact-right .btn {
		color: ' . $this->params->get('templateBackgroundColor') . ';
	}
	footer a {
		color: ' . $this->params->get('templateBackgroundColor') . ';
	}');
}

// Check for a custom CSS file
//JHtml::_('stylesheet', 'user.css', array('version' => 'auto', 'relative' => true));

// Check for a custom js file
//JHtml::_('script', 'user.js', array('version' => 'auto', 'relative' => true));

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Adjusting content width
if ($this->countModules('right') && $this->countModules('left'))
{
	$span = 'col-xs-12 col-md-7';
}
elseif ($this->countModules('right') && !$this->countModules('left'))
{
	$span = 'col-xs-12 col-md-10';
}
elseif (!$this->countModules('right') && $this->countModules('left'))
{
	$span = 'col-xs-12 col-md-9';
}
elseif ($this->countModules('payment-guide') && !$this->countModules('left')&& !$this->countModules('right'))
{
	$span = 'col-xs-12 col-md-4';
}
else
{
	$span = 'col-xs-12';
}
//about me
$myImage = '<img src="' . JUri::root() . $this->params->get('picFile') . '" alt="' . $sitename . '" />';
$mySiteTitle = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('sitetitle'), ENT_COMPAT, 'UTF-8') . '</span>';

// Logo file or site title param
if ($this->params->get('logoFile'))
{
	$logo = '<img src="' . JUri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '" />';
}
elseif ($this->params->get('sitetitle'))
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('sitetitle'), ENT_COMPAT, 'UTF-8') . '</span>';
}
else
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />
<!--script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="cb377db3-cdf9-4102-950b-1d8fab5a7855";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script-->
</head>
<body class="site <?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '')
	//. ($params->get('fluidContainer') ? ' fluid' : '')
	;
	echo ($this->direction === 'rtl' ? ' rtl' : '');
?>">

<header class="navbar navbar-inverse clearfix">
	
    <div id="header" class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
    
    	<div class="navbar-header clearfix">
        	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#collapse-menu" aria-expanded="false">
                <span class="sr-only"><?php echo JTEXT::_('TPL_MASS_TOGGLE_MENU'); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
			</button>
            
            <a class="navbar-brand" href="#"><?php echo $logo; ?></a>
        
        </div>
	
		<?php if ($this->countModules('menu')) : ?>
                
        <menu id="collapse-menu" class="navbar-right collapse navbar-collapse">
            <jdoc:include type="modules" name="menu" style="xhtml" /><!-- nav navbar-nav -->
        </menu>
        <?php endif; ?>
        
        <div id="social" class="clearfix">
        	<jdoc:include type="modules" name="social" style="xhtml" />
        </div>
    
    </div>

</header>

<?php if ($this->countModules('about-pic')||$this->countModules('about')) : ?>
<div id="mm-about" class="clearfix">
    <div id="about" class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?> clearfix">
    	<div class="row clearfix">
            <div id="logo" class="col-xs-12 col-md-6 clearfix">
                <?php
				if ($this->countModules('about-pic')){
                echo '<jdoc:include type="modules" name="about-pic" style="xhtml" />';
                } else {
				echo $myImage;
				}
				?>
            </div>
            
            <div id="discription" class="col-xs-12 col-md-6 clearfix">
                <?php 
                if ($this->countModules('about')){
                echo '<jdoc:include type="modules" name="about" style="xhtml" />';
                } else {
                echo $mySiteTitle;
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if ($this->countModules('slider')) : ?>
<div id="mm-slider" class="clearfix">
    <div id="slider" class="clearfix">
        <jdoc:include type="modules" name="slider" style="xhtml" />
    </div>
</div>
<?php endif; ?>



<?php if ($this->countModules('skills')) : ?>
<div id="mm-skills" class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?> clearfix">

	<div id="top-skills" class="row clearfix">
    	<jdoc:include type="modules" name="top-skills" style="xhtml" />
    </div>
    
    
    <div id="skills" class="row clearfix">
        <jdoc:include type="modules" name="skills" style="xhtml" />
    </div>
    
</div>
<?php endif; ?>

<main class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?> clearfix">
    <div id="main" class="row clearfix">
    
        <?php if ($this->countModules('left')) : ?>
        <aside class="col-xs-12 col-md-3 clearfix">
            <jdoc:include type="modules" name="left" style="xhtml" />
        </aside>
        <?php endif; ?>
        
        <article id="content" role="main" class="<?php echo $span; ?>">
            <jdoc:include type="message" />
            <jdoc:include type="component" />
        </article>
        
        <?php if ($this->countModules('right')) : ?>
        <nav class="col-xs-12 col-md-2 clearfix">
            <jdoc:include type="modules" name="right" style="xhtml" />
        </nav>
        <?php endif; ?>
        
        <?php if ($this->countModules('payment-guide')) : ?>
        <nav class="col-xs-12 col-md-8 clearfix">
            <jdoc:include type="modules" name="payment-guide" style="xhtml" />
        </nav>
        <?php endif; ?>
        
    </div>
</main>

<?php if ($this->countModules('portfolio')) : ?>
<div id="mm-portfolio" class="container<?php echo ($params->get('fluidContainer') ? '' : '-fluid'); ?> clearfix">
    <div id="portfolio" class="clearfix">
        <jdoc:include type="modules" name="portfolio" style="xhtml" />
    </div>
</div>
<?php endif; ?>

<?php if ($this->countModules('testimonial')) : ?>
<div id="mm-testimonial" class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?> clearfix">
    <div id="testimonial" class="clearfix">
        <jdoc:include type="modules" name="testimonial" style="xhtml" />
    </div>
</div>
<?php endif; ?>

<footer class="clearfix">
	<div id="mm-footer" class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?> clearfix">
    	
        <div id="footer-top" class="row clearfix">
        
                <a id="scrollup">
                    <?php echo JText::_('TPL_MASS_BACKTOTOP'); ?>
                </a>
            
            <?php if ($this->countModules('footer-custom')) : ?>
            <div id="footer-custom" class="col-xs-12 col-md-12 clearfix">        
            <jdoc:include type="modules" name="footer-custom" style="xhtml" /> 
            </div>
            <?php endif; ?>
        </div>
        
        
        <?php if ($this->countModules('news')||$this->countModules('services')
				||$this->countModules('contact')||$this->countModules('copyright')) : ?>	
        <div id="footer-1" class="row clearfix">
			
            
            <?php if ($this->countModules('contact-right')) : ?>
            <div id="contact-right" class="col-xs-12 col-md-4 clearfix">
                <jdoc:include type="modules" name="contact-right" style="xhtml" />
            </div>
            <?php endif; ?>
            
            
            <div id="contact-left" class="col-xs-12 col-md-8 clearfix">
                
                <div id="footer-2" class="row clearfix">
                    
                    <?php if ($this->countModules('news')) : ?>
                    <div id="news" class="col-xs-12 clearfix">
                        <jdoc:include type="modules" name="news" style="xhtml" />
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($this->countModules('services')) : ?>
                    <div id="services" class="col-xs-12 col-md-6 clearfix">
                        <jdoc:include type="modules" name="services" style="xhtml" />
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($this->countModules('contact')) : ?>
                    <div id="contact" class="col-xs-12 col-md-6 clearfix">
                        <jdoc:include type="modules" name="contact" style="xhtml" />
                    </div>
                    <?php endif; ?>
            
					<?php if ($this->countModules('copyright')) : ?>
                    <div id="copyright" class="col-xs-12 clearfix">
                        <jdoc:include type="modules" name="copyright" style="xhtml" />
                    </div>
                    <?php endif; ?>
            
                </div>
                
            </div>
            
            
        </div> 
        <?php endif; ?>   

        
	</div>
</footer>

	<jdoc:include type="modules" name="debug" style="none" />
</body>
<script type="text/javascript">
    jQuery(document).ready(function(){ 
 
        jQuery(window).scroll(function(){
            if (jQuery(this).scrollTop() > 100) {
                jQuery('#scrollup').fadeIn();
            } else {
                jQuery('#scrollup').fadeOut();
            }
        }); 
 
        jQuery('#scrollup').click(function(){
            jQuery("html, body").animate({ scrollTop: 0 }, 600);
            return false;
        });
 
    });
</script>
</html>
