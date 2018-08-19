<style>
img { max-width:100%;}
p { word-wrap: break-word;}
#mm-simpleportfolio { margin-top:2em; color:#333;}
#mm-simpleportfolio .modal-feature { 
	text-align:center;
	 margin-bottom:3em; 
	 padding-top:1em; 
	 padding-bottom:1em;
	 
	 box-shadow: 0 0 0 0 rgba(0,0,0,0.2);
    transition: 0.3s;
    border-radius: 5px;
}
#mm-simpleportfolio .modal-feature:hover {
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}
/*#mm-simpleportfolio .modal-feature:hover { background-color:rgba(0,255,227,0.5);}*/
#mm-simpleportfolio .modal-dialog { text-align:right;}
#mm-simpleportfolio .modal-dialog h4 {color:#000;}
</style>
<div id="mm-simpleportfolio" class="mm-simpleportfolio<?php echo $module->id; ?> row">

<?php
defined('_JEXEC') or die;

$featureShow = $params->get('onlyFeatureContent');


foreach ($data as $value): ?>

<?php  
$pictures 		   = json_decode($value->images,true);
$image_intro 		= $pictures['image_intro'];
$image_intro_alt    = $pictures['image_intro_alt'];
$image_item		 = '<img src="'. $image_intro .'" alt="'. $image_intro_alt .'" class="img-thumbnail"/>';
					//<img src="" alt="" class=""/>

$image_fulltext = $pictures['image_fulltext'];
$image_fulltext_alt = $pictures['image_fulltext_alt'];
$image_slider     = '<img src="'. $image_fulltext .'" alt="'. $image_fulltext_alt .'" style="width:100%;"/>';


$catid			  = $value->catid;
$id				 = $value->id;
$title			  = $value->title;
$featured 		   = $value->featured;

$slider 			 = "";
$item			   = "";

$introtext		  = $value->introtext;
$fulltext		   = $value->fulltext;
$xreference		 = $value->xreference;

$readmore		   = '<a class="btn btn-default" href="index.php?option=com_content&view=article&catid='. $catid .'&id='.  $id .'">'. $title .'</a>';

$itemID = 0;
$indicatorList = '<li data-target="#myCarousel" data-slide-to="'. $itemID .'"></li>';

?>



<div class="modal-feature col-xs-4 clearfix" data-toggle="modal" data-target="#myModal<?php echo '_'.$id; ?>">

	<img src="<?php echo $image_intro; ?>" alt="<?php echo $image_intro_alt; ?>" class="img-thumbnail">
	<p class="h3"><?php echo $title; ?></p>
    <div class="text-info"><?php echo $introtext; ?></div>
    <a class="btn btn-default" role="button">مشاهده جزئیات پروژه</a>

</div>

  <!-- Modal -->
<div class="modal fade" id="myModal<?php echo '_'.$id; ?>" role="dialog">
	<div class="modal-dialog modal-lg">
    
		<!-- Modal content-->
		<div class="modal-content">
        
			<div class="modal-header">
				<button type="button" class="close pull-left" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php echo $title; ?></h4>
                <img src="<?php echo $image_fulltext; ?>" alt="<?php echo $image_fulltext_alt; ?>" class="img-thumbnail"/>
			</div>
            
			<div class="modal-body">
				
                <?php echo $fulltext; ?>
                
			</div>
            
			<div class="modal-footer">
   				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                
                <?php if ($xreference!= ''):?>
                <a class="btn btn-success" target="new" href="<?php echo $xreference; ?>" role="button"><?php echo 'مشاهده نسخه آنلاین '.$title; ?></a>
                <?php else : ?>
                <a class="btn btn-warning " target="new" disabled="disabled" href="<?php echo $xreference; ?>" role="button"><?php echo 'آنلاین نیست'; ?></a>
                <?php endif;?>
                
			</div>
            
		</div>
      
	</div>
</div>
<?php
endforeach;
?>
</div>
