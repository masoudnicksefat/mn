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



//get Helper class

$data = modSimpleportfolioHelper::getArticle($count,$customIds,$featureShow);

//$cf = modAzarArticleHelper::countFeatured();

$document = JFactory::getDocument();

//get Layout files
require JModuleHelper::getLayoutPath('mod_simpleportfolio', $params->get('layout', 'default'));


