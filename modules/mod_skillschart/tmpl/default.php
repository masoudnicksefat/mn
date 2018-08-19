<?php 

defined('_JEXEC') or die;
$document = JFactory::getDocument();

$id = $module->id;

$skillName = $params->get('skillName');
$skillCount = $params->get('skillCount');
$skillImage = $params->get('skillImage');
$skillDscrpt = $params->get('skillDscrpt');

$dataColor = $params->get('dataColor');
$dataBackground = $params->get('dataBackground');

$skillWidth = $params->get('skillWidth');
$skillHeight = $params->get('skillHeight');

//echo "<pre>".print_r($data ,1)."</pre>";
//echo $skillImage;

JHtml::_('bootstrap.framework');

// Add JavaScript Frameworks
JHtml::_('jquery.framework');

#JHtml::_('script', 'modules/mod_skillschart/js/tox-progress.js', array('version' => 'auto', 'relative' => true));
#JHtml::_('stylesheet', 'modules/mod_skillschart/css/tox-progress.css', array('version' => 'auto', 'relative' => true));

JHtml::script(Juri::base() . 'modules/mod_skillschart/js/tox-progress.js');
JHtml::stylesheet(Juri::base() . 'modules/mod_skillschart/css/tox-progress.css');
?>
<!--link href="modules/mod_skillschart/css/tox-progress.css" rel="stylesheet" type="text/css"-->
<style>
	.black{color:black !important;}
</style>
<!--script
	src="https://code.jquery.com/jquery-3.2.1.min.js"
	integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
	crossorigin="anonymous">
</script>
<script 
	src="modules/mod_skillschart/js/tox-progress.js">
</script-->
<div class="clearfix" style="height: <?php echo $skillHeight; ?>px; margin: 2em 0 50px;">
    <div class="tox-progress" data-size="180" data-thickness="12" data-color="<?php echo $dataColor; ?>" data-background="<?php echo $dataBackground; ?>" data-progress="<?php echo $skillCount; ?>" data-speed="1500">
        <div class="tox-progress-content" data-vcenter="true">
        	<?php if($skillImage) {
				echo '<img height="90" style="margin:auto" src="'. $skillImage.'"/>';
			} elseif($skillDscrpt) {
				echo '<p>'.$skillDscrpt.'</p>';	
			} else {
				echo '<img height="90" style="margin:auto" src="modules/mod_skillschart/img/iconOfficeSkills.png"/>';
			}
			
			?>	
        </div>
    </div>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            ToxProgress.create();
            ToxProgress.animate();
        });
    </script>
    <p class="h3 black text-center"><?php echo $skillName; ?></p>
    <p id="percent<?php echo $id; ?>" style="text-align:center;">0</p>
    <script>
      var percent<?php echo $id; ?> = document.getElementById("percent<?php echo $id; ?>"); 
      var num<?php echo $id; ?> = 0;
      
      var id<?php echo $id; ?> = setInterval(frame, 15);
      
      function frame() {
        if (num<?php echo $id; ?> == <?php echo $skillCount; ?>) {
          clearInterval(id<?php echo $id; ?>);
        } else {
          num<?php echo $id; ?>++;
          percent<?php echo $id; ?>.innerHTML = num<?php echo $id; ?> + '%';
        }
      }
    </script>
</div>