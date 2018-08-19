<?php 

defined('_JEXEC') or die;

//require_once __DIR__ .'/helper.php';
require_once dirname(__FILE__).'/helper.php' ;


$count = $params->get('count');

$featureShow = $params->get('onlyFeatureContent');

$jCat = $params->get('catId');
$customIds ="";

foreach($jCat as $key => $value)
{
     $customIds .= $value .',';
}
$customIds = trim($customIds, ',');


$modDir = JURI::root() . 'modules/mod_simpletestimonial/';
//get Helper class

$data = modSimpletestimonialHelper::getArticle($count,$customIds,$featureShow);

//$cf = modAzarArticleHelper::countFeatured();

$document = JFactory::getDocument();

//get Layout files
require JModuleHelper::getLayoutPath('mod_simpletestimonial', $params->get('layout', 'default'));

//echo '<pre dir=ltr >'.print_r($data,1).'</pre>';

//echo '<pre dir=ltr >'.print_r($data[0]->title,1).'</pre>';
