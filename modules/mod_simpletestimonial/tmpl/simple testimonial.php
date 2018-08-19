<?php
defined('_JEXEC') or die;
$doc = JFactory::getDocument();

$featureShow = $params->get('onlyFeatureContent');
#$testimonialBackgroundColor = $params->get('testimonialBackgroundColor');

// Template color
if ($params->get('testimonialBackgroundColor'))
{
	$doc->addStyleDeclaration('
#mm-simpleTestimonial {
	margin-bottom : 2em;
	margin-top: 1em;
}
.pic {
	max-width:200px;
	margin-bottom: 1.7em;
}
.testimonial {
	min-height: 1px;
	padding-left: 15px;
	padding-right: 15px;
	position: relative;
	
}
.testimonial-title {
  height: 2.2em;
}
.testimonial-review {
	background-color:' . $params->get('testimonialBackgroundColor') . ';
	border-radius:5px;
	color:' . $params->get('testimonialFontColor') . ';
	padding:1em;
	position:relative;
	margin-top:2em;
}
.testimonial-review::before {
	content:"";
	width:30px;
	height:30px;
	display:block;
	border-bottom:15px solid ' . $params->get('testimonialBackgroundColor') . ';
	border-top:15px solid transparent;
	border-right:15px solid transparent;
	border-left:15px solid transparent;
	position:absolute;
	z-index:1000;
	top:-30px;
	right:46%;
}');
}
?>
<style>

</style>
<div id="mm-simpleTestimonial" class="simple-testimonial<?php echo $module->id; ?> row">

<?php


/*
$catid			  = $data->catid;
$id				 = $data->id;
$title			  = $data->title;
$featured 		   = $data->featured;

$slider 			 = "";
$item			   = "";

$introtext		  = $data->introtext;
$fulltext		   = $data->fulltext;
$xreference		 = $data->xreference;

$readmore		   = '<a class="btn btn-default" href="index.php?option=com_content&view=article&catid='. $catid .'&id='.  $id .'">'. $title .'</a>';

$itemID = 0;
$indicatorList = '<li data-target="#myCarousel" data-slide-to="'. $itemID .'"></li>';
*/

// Number content per row -> get from user
$contentPerRow = (int) $params->get('contentPerRow', 3);


$dataCount = count($data); // store count of data
$rowNum = ceil($dataCount/$contentPerRow); // calculate count of rows
$printedIndex = 0; // store printed content index

?>

<?php for ($r=1; $r<=$rowNum; $r++ ) :?>
	<div id="row-<?php echo $r; ?>" class="clearfix">
		<?php 
		$endOfContentFor = $printedIndex + $contentPerRow; // calculate end of for
		for($i=$printedIndex; $i<$endOfContentFor; $i++) :
			$value = $data[$i]; // object of data
			
			// get picture data
			$pictures 		   = json_decode($value->images,true);
			$image_intro 		= $pictures['image_intro'];
			$image_intro_alt    = $pictures['image_intro_alt'];
			$image_item		 = '<img src="'. $image_intro .'" alt="'. $image_intro_alt .'" class="img-thumbnail"/>';

			$image_fulltext = $pictures['image_fulltext'];
			$image_fulltext_alt = $pictures['image_fulltext_alt'];
			$image_slider     = '<img src="'. $image_fulltext .'" alt="'. $image_fulltext_alt .'" style="width:100%;"/>';

		?>
			<?php #if( $contentPerRow < $dataCount ): ?> 
			<div class="testimonial pull-right col-xs-12 col-md-4 clearfix">
				<div class="pic center-block">
					<img src="<?php echo $image_intro; ?>" alt="<?php echo $image_intro_alt; ?>" class="img-circle">
				</div>
				<p class="h4 testimonial-title">
					<?php echo $value->title; ?>
				</p>
				<div class="testimonial-review">
					<?php echo $value->introtext; ?>
				</div>
			</div>
            <?php #endif; ?>
		<?php 
			$printedIndex++;
		endfor; 
	    ?>
	</div>
<?php endfor;?>    

</div>
