<?php
class modSimpletestimonialHelper
{
    public static function getArticle($count,$customIds,$featureShow)
    {
         // Get a db connection.
        $db = JFactory::getDbo();
		$catid = 'catid IN ('.$customIds.')';
		$featureItem = 'featured = '.$featureShow; 
		//echo $db->getPrefix();

        // Create a new query object.
        $query = $db->getQuery(true);
		
        $query->select('*');
        $query->from('#__content');
        $query->where('state = 1');
		$query->where($catid);
		$query->where($featureItem);
        $query->order('created DESC');
        $query->setLimit($count);
		
		
        $db->setQuery($query);
		
        $result = $db->loadObjectList();
		        
        return $result;

    }
	
}