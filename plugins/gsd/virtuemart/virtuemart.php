<?php

/**
 * @package         Google Structured Data
 * @version         3.1.7 Pro
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_gsd/helpers/plugin.php';

/**
 *  VirtueMart Google Structured Data Plugin
 */
class plgGSDVirtueMart extends GSDPlugin
{
    /**
     *  ID Column name
     *
     *  @var  string
     */
	protected $column_id = 'virtuemart_product_id';

    /**
     *  Title column name
     *
     *  @var  string
     */
	protected $column_title = 'product_name';

    /**
     *  Indicates the query string parameter name that is used by the front-end component
     *
     *  @var  string
     */
    protected $thingRequestIDName = 'virtuemart_product_id';

    /**
     *  Indicates the default content type which can be used to automatically produce the structured data
     *
     *  @var  mixed
     */
    protected $defaultContentType = 'product';

	/**
     *  Product View
     *  
     *  @return  object
     */
	protected function viewProductDetails()
	{
        $id           = $this->getThingID();
        $productModel = VmModel::getModel('Product');
        $product      = $productModel->getProduct($id);

        // Rating
        $rating = $this->getRating($id);

        // Currency
        $currency_model = VmModel::getModel('currency');
        $currency = $currency_model->getCurrency($product->prices['product_currency']);

        // Prepare Data
        $data = array(
            "headline"    => $product->product_name,
            "description" => $product->product_desc,
            "offerPrice"  => GSDHelper::formatPrice($product->prices['salesPrice']),
            "currency"    => $currency->currency_code_3,
            "image"       => $this->getImage($product->images),
            "brand"       => $product->mf_name,
            "ratingValue" => $rating["value"],
            "reviewCount" => $rating["count"],
            "sku"         => $product->product_sku
        );

        return $data;
	}

    /**
     *  Gets Virtuemart product rating
     *
     *  @param   integer  $id  Product ID
     *
     *  @return  array
     */
    private function getRating($id)
    {
        $ratingModel = VmModel::getModel('ratings');
        $rating = $ratingModel->getRatingByProduct($id);

        if (!is_object($rating) || !isset($rating->rating))
        {
            return array(
                "value" => 0,
                "count" => 0
            );
        }

        return array(
            "value" => $rating->rating,
            "count" => $rating->ratingcount
        );
    }

    /**
     *  Returns VirtueMart product image
     *
     *  @param   array  $images   Product Images
     *
     *  @return  string
     */
    private function getImage($images)
    {
        if (!is_array($images) || count($images) == 0 || !isset($images[0]) || !isset($images[0]->file_url))
        {
            return;
        }

        return GSDHelper::absURL($images[0]->file_url);
    }

    /**
     *  VirtueMart is using a different table per language.
     *  Therefore, we need to use their API to get the default products table
     *
     *  @return  string
     */
    protected function getTable()
    {
        if (!class_exists('VmConfig'))
        {
            require_once JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php'; 
        }
            
        // Init configuration
        VmConfig::loadConfig();

        // Get virtuemart language. Default to en_gb if the configuration returns an invalid language.
        $lang = VmConfig::$vmlang;
        $langSuffix = (is_null($lang) || empty($lang)) ? 'en_gb' : $lang;

        return 'virtuemart_products_' . $langSuffix;
    }

    /**
     *  Return plugin's list query
     *
     *  @param   JModel  $model  The Things Model
     *
     *  @return  Query
     */
    protected function getListQuery($model)
    {
        $db = $this->db;

        $state = $model->getState();

        // Select records from the database
        $query = $db->getQuery(true)
            ->select(array(
                $db->quoteName('a.' . $this->column_id, 'id'),
                $db->quoteName('a.' . $this->column_title, 'title'),
                $db->quoteName('b.published', 'state')
            ))
            ->from($db->quoteName('#__' . $this->getTable()) . 'AS a')
            ->join('INNER', $db->quoteName('#__virtuemart_products', 'b') . ' ON (' . $db->quoteName('a.'. $this->column_id) . ' = ' . $db->quoteName('b.' . $this->column_id) . ')');


        // Filter by search in title.
        $search = $state->get('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where($this->column_id . ' = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where($this->column_title . ' LIKE ' . $search);
            }
        }

        // Add the list ordering clause.
        $query->order($db->escape('a.' . $this->column_id) . ' ' . $db->escape('DESC'));

        return $query;
    }
}
