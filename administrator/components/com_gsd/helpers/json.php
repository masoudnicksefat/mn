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

defined('_JEXEC') or die('Restricted Access');

/**
 *  Google Structured Data JSON generator
 */
class GSDJSON
{
	/**
	 *  Content Type Data
	 *
	 *  @var  object
	 */
	private $data;

    /**
     *  List of available content types
     *
     *  @var  array
     */
    private $contentTypes = array(
        
        'course',
        'event',
        'product',
        'recipe',
        'review',
        'factcheck',
        'video',
        'custom_code',
        
        'article'
    );

	/**
	 *  Class Constructor
	 *
	 *  @param  object  $data
	 */
	public function __construct($data = null)
	{
		$this->setData($data);
	}

    /**
     *  Get Content Types List
     *
     *  @return  array
     */
    public function getContentTypes()
    {
        $types = $this->contentTypes;
        asort($types);

        // Move Custom Code option to the end
        if ($customCodeIndex = array_search('custom_code', $types))
        {
            unset($types[$customCodeIndex]);
            $types[] = 'custom_code';
        }

        return $types;
    }

	/**
	 *  Set Data
	 *
	 *  @param  array  $data
	 */
	public function setData($data)
	{
		if (!is_array($data))
		{
			return;
		}

		$this->data = new JRegistry($data);
		return $this;
	}

	/**
	 *  Get Content Type result
	 *
	 *  @return  string
	 */
	public function generate()
	{
		$contentTypeMethod = 'contentType' . $this->data->get('contentType');

        // Make sure we have a valid Content Type
		if (!method_exists($this, $contentTypeMethod) || !$content = $this->$contentTypeMethod())
		{
            return;
		}

        // In case we have a string (See Custom Code), return the original content.
        if (is_string($content))
        {
            return $content;
        }

        // In case we have an array, transform it into JSON-LD format.
        // Always prepend the @context property
        $content = ['@context' => 'https://schema.org'] + $content;

return '
<script type="application/ld+json">
'
    . json_encode($content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK) . 
'
</script>';
	}

    /**
     *  Constructs the Breadcrumbs Snippet
     *
     *  @return  array
     */
    private function contentTypeBreadcrumbs()
    {
        $crumbs = $this->data->get('crumbs');
        
        if (!is_array($crumbs))
        {
            return;
        }

        $crumbsData = [];

        foreach ($crumbs as $key => $value)
        {
            $crumbsData[] = [
                '@type'    => 'ListItem',
                'position' => ($key + 1),
                'item'     => [
                    '@id'  => $value->link,
                    'name' => $value->name
                ]
            ];
        }

        return [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $crumbsData
        ];
    }

    /**
     *  Constructs the Site Name Snippet
     *  https://developers.google.com/structured-data/site-name
     *
     *  @return  array
     */
    private function contentTypeSiteName()
    {
        $content = [
            '@type' => 'WebSite',
            'name'  => $this->data->get('name'),
            'url'   => $this->data->get('url')
        ];

        if ($this->data->get('alt'))
        {
            $content = array_merge($content, [
                'alternateName' => $this->data->get('alt')
            ]);
        }

        return $content;
    }

    /**
     *  Constructs the Sitelinks Searchbox Snippet
     *  https://developers.google.com/search/docs/data-types/sitelinks-searchbox
     *
     *  @return  array 
     */
    private function contentTypeSearch()
    {
        return [
            '@type' => 'WebSite',
            'url'   => $this->data->get('siteurl'),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => $this->data->get('searchurl'),
                'query-input' => 'required name=search_term'  
            ]
        ];
    }

    /**
     *  Constructs Site Logo Snippet
     *  https://developers.google.com/search/docs/data-types/logo
     *
     *  @return  array
     */
    private function contentTypeLogo()
    {
        return [
            '@type' => 'Organization',
            'url'   => $this->data->get('url'),
            'logo'  => $this->data->get('logo')
        ];
    }

	/**
	 *  Constructs the Article Content Type
	 *
	 *  @return  array
	 */
	private function contentTypeArticle()
	{
        $content = [
            '@type' => 'Article',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => $this->data->get('url')
            ],
            'headline'    => $this->data->get('title'),
            'description' => $this->data->get('description'),
            'image' => [
                '@type'  => 'ImageObject',
                'url'    => $this->data->get('image'),
                'height' => 800,
                'width'  => 800
            ]
        ];

		// Author
		if ($this->data->get('authorName'))
		{
            $content = array_merge($content, [
                'author' => [
                    '@type' => 'Person',
                    'name'  => $this->data->get('authorName')
                ]
            ]);
		}

		// Publisher
		if ($this->data->get('publisherName'))
		{
            $content = array_merge($content, [
                'publisher' => [
                    '@type' => 'Organization',
                    'name'  => $this->data->get('publisherName'),
                    'logo'  => [
                        '@type'  => 'ImageObject',
                        'url'    => $this->data->get('publisherLogo'),
                        'width'  => 600,
                        'height' => 60
                    ]
                ]
            ]);  
		}

        
        // Aggregate Rating
        $this->addRating($content);
        

        return $this->addDate($content);
	}

    
    /**
     *  Constructs the Product Content Type
     *  https://developers.google.com/search/docs/data-types/products
     *
     *  @return  array
     */
    private function contentTypeProduct()
    {
        $content = [
            '@type'       => 'Product',
            'name'        => $this->data->get('title'),
            'image'       => $this->data->get('image'),
            'description' => $this->data->get('description')
        ];

        // Brand
        if ($this->data->get('brand'))
        {
            $content = array_merge($content, [
                'brand' => [
                    '@type' => 'Thing',
                    'name'  => $this->data->get('brand')
                ]
            ]);
        }

        // SKU
        if ($this->data->get('sku'))
        {
            $content = array_merge($content, [
                'sku' => $this->data->get('sku')
            ]);
        }

        // Offer / Pricing
        if ((int) $this->data->get('offerPrice') > 0)
        {
            $content = array_merge($content, [
                'offers' => [
                    '@type'         => 'Offer',
                    'priceCurrency' => $this->data->get('currency', 'USD'),
                    'price'         => $this->data->get('offerPrice'),
                    'url'           => $this->data->get('url'),
                    'itemCondition' => $this->data->get('condition', 'http://schema.org/NewCondition'),
                    'availability'  => $this->data->get('availability', 'http://schema.org/InStock')
                ]
            ]);
        }

        // Aggregate Rating
        $this->addRating($content);

        return $content;
    }

	/**
	 *  Constructs the Event Content Type
	 *  https://developers.google.com/search/docs/data-types/events
	 *
	 *  @return  array
	 */
	private function contentTypeEvent()
	{
        $content = [
            '@type'       => $this->data->get('type'),
            'name'        => $this->data->get('title'),
            'image'       => $this->data->get('image'),
            'description' => $this->data->get('description'),
            'url'         => $this->data->get('url'),
            'startDate'   => $this->data->get('startdate'),
            'doorTime'    => $this->data->get('starttime'),
            'endDate'     => $this->data->get('enddate'),
            'eventStatus' => $this->data->get('status'),
            'location'    => [
                '@type'   => 'EventVenue',
                'name'    => $this->data->get('location.name'),
                'address' => $this->data->get('location.address')
            ]
        ];

		if ($this->data->get('performer.name') && ($this->data->get('type') != 'SportsEvent'))
		{
            $content = array_merge($content, [
                'performer' => [
                    '@type' => $this->data->get('performer.type'),
                    'name'  => $this->data->get('performer.name')
                ]
            ]);
		}

        if ($this->data->get('hometeam'))
        {
            $content = array_merge($content, [
                'homeTeam' => [
                    '@type' => 'SportsTeam',
                    'name'  => $this->data->get('hometeam')
                ]
            ]);
        }

        if ($this->data->get('awayteam'))
        {
            $content = array_merge($content, [
                'awayTeam' => [
                    '@type' => 'SportsTeam',
                    'name'  => $this->data->get('awayteam')
                ]
            ]);
        }

        $content = array_merge($content, [
            'offers' => [
                '@type'         => 'Offer',
                'category'      => 'primary',
                'url'           => $this->data->get('url'),
                'availability'  => $this->data->get('offer.availability'),
                'validFrom'     => $this->data->get('offer.startDateTime'),
                'price'         => $this->data->get('offer.price'),
                'priceCurrency' => $this->data->get('offer.currency'),
                'inventoryLevel' => [
                    '@context' => 'https://schema.org',
                    '@type'    => 'QuantitativeValue',
                    'value'    => $this->data->get('offer.inventoryLevel'),
                    'unitText' => 'Tickets'
                ]
            ]
        ]);

		return $content;
	}

	/**
	 *  Constructs the Social Profiles Snippet
	 *  https://developers.google.com/search/docs/data-types/social-profile-links
	 *
	 *  @return  array
	 */
	private function contentTypeSocialProfiles()
	{
        return [
            '@type'  => $this->data->get('type'),
            'name'   => $this->data->get('sitename'),
            'url'    => $this->data->get('siteurl'),
            'sameAs' => array_values((array) $this->data->get('links'))
        ];
	}

    /**
     *  Constructs the Business Listing Content Type
     *  https://developers.google.com/search/docs/data-types/local-businesses
     *
     *  @return  array
     */
    private function contentTypeBusinessListing()
    {
        $content = [
            '@type' => $this->data->get('type'),
            '@id'   => $this->data->get('id'),
            'name'  => $this->data->get('name'),
            'image' => $this->data->get('image'),
            'url' => $this->data->get('id'),
            'telephone' => $this->data->get('telephone'),
            'priceRange' => $this->data->get('price_range'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress'   => $this->data->get('streetAddress'),
                'addressLocality' => $this->data->get('addressLocality'),
                'addressRegion'   => $this->data->get('addressRegion'),
                'postalCode'      => $this->data->get('postalCode'),
                'addressCountry'  => $this->data->get('addressCountry')
            ],
            'geo' => [
                '@type'     => 'GeoCoordinates',
                'latitude'  => $this->data->get('coordinates')[0],
                'longitude' => $this->data->get('coordinates')[1]
            ]
        ];

        $hoursAvailable = (int) $this->data->get('hoursAvailable', '0');

        // Always Open
        if ($hoursAvailable == 1)
        {
            $content = array_merge($content, [
                'openingHoursSpecification' => [
                    '@type'     =>  'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    'opens'     => '00:00',
                    'closes'    => '23:59'
                ] 
            ]);
        }

        // Selected Dates
        if ($hoursAvailable == 2)
        {
            $weekDays = $this->data->get('weekDays');
            $openingHours = [];

            foreach ($weekDays as $weekDay)
            {
                if (!$this->data->get($weekDay))
                {
                    continue;
                }

                $openingHours[] = [
                    '@type'     => 'OpeningHoursSpecification',
                    'dayOfWeek' => $weekDay,
                    'opens'     => $this->data->get($weekDay . '_start', '00:00'),
                    'closes'    => $this->data->get($weekDay . '_end', '23:59')
                ];
            }

            if ($openingHours)
            {
                $content = array_merge($content, [
                    'openingHoursSpecification' => $openingHours
                ]);             
            }
        }

        return $content;
    }

	/**
	 *  Constructs the Recipe Content Type
	 *  https://developers.google.com/search/docs/data-types/recipes
	 *
	 *  @return  array
	 */
	private function contentTypeRecipe()
	{
        $content = [
            '@type'        => 'Recipe',
            'name'         => $this->data->get('title'),
            'image'        => $this->data->get('image'),
            'description'  => $this->data->get('description'),
            'prepTime'     => $this->data->get('prepTime'),
            'cookTime'     => $this->data->get('cookTime'),
            'totalTime'    => $this->data->get('totalTime'),
            'nutrition'    => [
                '@type'    => 'NutritionInformation',
                'calories' => $this->data->get('calories')
            ], 
            'recipeYield'        => $this->data->get('yield'),
            'recipeIngredient'   => $this->data->get('ingredient'),
            'recipeInstructions' => $this->data->get('instructions')
        ];

		// Author Data
		if ($this->data->get('authorName'))
		{
            $content = array_merge($content, [
                'author' => [
                    '@type' => 'Person',
                    'name'  => $this->data->get('authorName')
                ]
            ]);
		}

        $this->addRating($content);
        $this->addDate($content);

		return $content;
	}

    /**
     *  Constructs the Course Content Type
     *  https://developers.google.com/search/docs/data-types/courses
     *
     *  @return  array
     */
	private function contentTypeCourse()
	{
        $content = [
            '@type' => 'Course',
            'name'  => $this->data->get('title'),
            'description' => $this->data->get('description'),
            'provider' => [
                '@type'  => 'Organization',
                'name'   => $this->data->get('sitename'),
                'sameAs' => $this->data->get('siteurl')
            ]
        ];

        if ($this->data->get('image'))
        {
            $content = array_merge($content, [
                'image' => [
                    '@type'  => 'ImageObject',
                    'url'    => $this->data->get('image'),
                    'height' => 800,
                    'width'  => 800
                ]
            ]);
        }

        $this->addRating($content);
        $this->addDate($content);

        return $content;
	}

    /**
     *  Constructs the Review Content Type
     *  https://developers.google.com/search/docs/data-types/reviews
     *
     *  @return  array
     */
    private function contentTypeReview()
    {
        $content = [
            '@type' => 'Review',
            'name' => $this->data->get('title'),
            'description' => $this->data->get('description'),
            'author' => [
                '@type'  => 'Person',
                'name'   => $this->data->get('authorName'),
                'sameAs' => $this->data->get('siteurl')
            ],
            'url' => $this->data->get('url'), 
            'publisher'  => [
                '@type'  => 'Organization',
                'name'   => $this->data->get('sitename'),
                'sameAs' => $this->data->get('siteurl')
            ],
            'itemReviewed' => [
                '@type' => $this->data->get('itemReviewedType'),
                'image' => $this->data->get('image'),
            ]
        ];
        
        if ($this->data->get('itemReviewedType') == 'LocalBusiness') 
        {
            $content = array_merge($content, [
                'address' => [
                    '@type' => 'PostalAddress',
                    'name'  => $this->data->get('address')
                ],
                'priceRange' => $this->data->get('priceRange'),
                'telephone'  => $this->data->get('telephone')
            ]);
        }

        if ($this->data->get('ratingValue'))
        {
            $content = array_merge($content, [
                'reviewRating' => [
                    'ratingValue' => $this->data->get('ratingValue'),
                    'worstRating' => $this->data->get('worstRating', 0),
                    'bestRating'  => $this->data->get('bestRating', 5)
                ]
            ]);
        }

        return $this->addDate($content);
    }

    /**
     *  Constructs the Fact Check Content Type
     *  https://developers.google.com/search/docs/data-types/factcheck
     *
     *  @return  array
     */
    private function contentTypeFactCheck()
    {
        $content = [
            '@type' => 'ClaimReview',
            'url' => $this->data->get('factcheckURL'),
            'itemReviewed' => [
                '@type'  => 'CreativeWork',
                'author' => [
                    '@type'  => $this->data->get('claimAuthorType'),
                    'name'   => $this->data->get('claimAuthorName'),
                    'sameAs' => $this->data->get('claimURL')
                ],
                'datePublished' => $this->data->get('claimDatePublished')
            ],
            'claimReviewed' => $this->data->get('title'),
            'author' => [
                '@type' => 'Organization',
                'name'  => $this->data->get('sitename')
            ],
            'reviewRating' => [
                '@type'         => 'Rating',
                'ratingValue'   => $this->data->get('factcheckRating'),
                'bestRating'    => $this->data->get('bestFactcheckRating'),
                'worstRating'   => $this->data->get('worstFactcheckRating'),
                'alternateName' => $this->data->get('alternateName')
            ]
        ];

        return $this->addDate($content);
    }

    /**
     *  Constructs the Video Content Type
     *  https://developers.google.com/search/docs/data-types/videos
     *
     *  @return  array
     */
    private function contentTypeVideo()
    {
        return [
            '@type'        => 'VideoObject',
            'name'         => $this->data->get('title'),
            'description'  => $this->data->get('description'),
            'thumbnailUrl' => $this->data->get('image'),
            'uploadDate'   => $this->data->get('datePublished'),
            'contentUrl'   => $this->data->get('contentUrl')
        ];
    }

    /**
     *  Constructs the Custom Code Content Type
     *
     *  @return  string    The custom code entered by user
     */
    private function contentTypeCustom_Code()
    {
        return $this->data->get('custom', '');
    }
    
    
    /**
     *  Appends the aggregateRating property to object
     *
     *  @param  array  &$content
     */
    private function addRating(&$content)
    {
        if (!$this->data->get('ratingValue') || !$this->data->get('reviewCount'))
        {
            return;
        }

        return $content = array_merge($content, [
            'aggregateRating' => [
                'ratingValue' => $this->data->get('ratingValue'),
                'reviewCount' => $this->data->get('reviewCount'),
                'worstRating' => $this->data->get('worstRating', 0),
                'bestRating'  => $this->data->get('bestRating', 5)
            ]
        ]);
    }

    /**
     *  Appends date properties to object
     *
     *  @param  array  &$content
     */
    private function addDate(&$content)
    {
        return $content = array_merge($content, [
            'datePublished' => $this->data->get('datePublished'),
            'dateCreated'   => $this->data->get('dateCreated'),
            'dateModified'  => $this->data->get('dateModified')
        ]);
    }
}

?>