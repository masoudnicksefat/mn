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
 *  JShopping Google Structured Data Plugin
 */
class plgGSDJShopping extends GSDPlugin
{
    /**
     *  Database table name holds the items
     *
     *  @var  string
     */
    protected $table = 'jshopping_products';

    /**
     *  ID Column name
     *
     *  @var  string
     */
	protected $column_id = 'product_id';

    /**
     *  State column name
     *
     *  @var  string
     */
    protected $column_state = "product_publish";

    /**
     *  Indicates the query string parameter name that is used by the front-end component
     *
     *  @var  string
     */
    protected $thingRequestIDName = 'product_id';

    /**
     *  Indicates the default content type which can be used to automatically produce the structured data
     *
     *  @var  mixed
     */
    protected $defaultContentType = 'product';

    /**
     *  Active Product Object
     *
     *  @var  object
     */
    private $product;

    /**
     *  JoomShopping Configuration Class
     *
     *  @var  class
     */
    private $config;

    /**
     *  Get View Name
     *
     *  @return  string  Return the current executed view in the front-end
     */
    protected function getView()
    {
        $input = $this->app->input;

        if ($input->get('controller') == 'product')
        {
            if ($input->get('view') == 'product' || $input->get('task') == 'view')
            {
                return 'product';
            }
        }

        return $this->app->input->get('view');
    }

    /**
     *  Product View
     *  
     *  @return  object
     */
    protected function viewProduct()
    {
        // Load JShopping class and current product data
        if (!$this->loadFactory() || !$this->loadProduct())
        {
            return;
        }

        // Product Brand
        $brand = $this->product->getManufacturerInfo();
        $brand = isset($brand->name) ? $brand->name : "";

        // Prepare Data
        $data = array(
            "headline"    => $this->product->getName(),
            "description" => $this->product->getDescription(),
            "offerPrice"  => GSDHelper::formatPrice($this->product->getPrice()),
            "currency"    => $this->getProductCurrencyCode(),
            "image"       => $this->getProductImage(),
            "brand"       => $brand,
            "ratingValue" => $this->product->average_rating,
            'bestRating'  => 10,
            "reviewCount" => $this->product->reviews_count,
            "sku"         => $this->product->product_ean
        );

        return $data;
    }

    /**
     *  Get plugin assosiated table columns.
     *  
     *  Since JShopping is using a different column per language, 
     *  we need to get the default language from its configuration.
     *
     *  @param   string  $column_name  The column name
     *
     *  @return  string
     */
    protected function getColumn($column_name)
    {
        if ($column_name == 'title' && $this->loadFactory())
        {
            $this->column_title = 'name_' . $this->config->defaultLanguage;
        }

        return parent::getColumn($column_name);
    }

    /**
     *  Initialize JShopping Classes
     *
     *  @return  bool
     */
    private function loadFactory()
    {
        require_once JPATH_SITE . "/components/com_jshopping/lib/factory.php";

        if (!class_exists('JSFactory'))
        {
            return;
        }

        $this->config = JSFactory::getConfig();
        return true;
    }

    /**
     *  Load JShopping current product data
     *
     *  @return  bool  
     */
    private function loadProduct()
    {
        $product = JSFactory::getTable('product', 'jshop');
        $product->load($this->getThingID());

        if (!$product instanceof jshopProduct)
        {
            return;
        }

        $this->product = $product;
        return true;
    }

    /**
     *  Get Product Main Image
     *
     *  @return  string
     */
    private function getProductImage()
    {
        $images = $this->product->getImages();

        if (!is_array($images) || count($images) == 0)
        {
            return;
        }

        return $this->config->image_product_live_path . '/' . $images[0]->image_full;
    }

    /**
     *  Get Product currency code (EUR, USD)
     *
     *  @return  string       
     */
    private function getProductCurrencyCode()
    {
        $table = JSFactory::getTable('currency', 'jshop');
        $table->load($this->product->currency_id);

        return isset($table->currency_code) ? $table->currency_code : "";
    }
}
