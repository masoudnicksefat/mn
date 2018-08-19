<?php 

defined('_JEXEC') or die;


//echo "<pre>".print_r($data ,1)."</pre>";

foreach ($data as $value): ?>
<?php $pictures = json_decode($value->images,true);?>


<div class="clearfix" style="width:25%;float:right;">

<h4 class="mymod_title"><?php echo $value->title; ?></h4> 

<img 
src="<?php echo $pictures['image_intro'];?>"
alt="<?php echo $pictures['image_intro_alt'];?>"
/>  
<p><?php echo $value->introtext; ?></p>
<img 
src="<?php echo $pictures['image_fulltext'];?>"
alt="<?php echo $pictures['image_fulltext_alt'];?>"
/>
<p><?php echo $value->fulltext; ?></p>
<a href="index.php?option=com_content&view=article&catid=<?php echo $value->catid; ?>&id=<?php echo $value->id; ?>"><?php echo $value->title; ?></a>
<?php //echo $url; ?>


</div>

<?php endforeach; ?>