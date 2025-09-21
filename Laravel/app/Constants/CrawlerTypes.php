<?php 

namespace App\Constants;

class CrawlerTypes
{
    public const  ALL_STEP = ['static', 'dynamic', 'paginated', 'authenticated', 'api', 'seed', 'two_step'];

    public const FIRST_STEP = ['seed','dynamic'];
    
    public const SECOND_STEP = ['static' , 'dynamic' , 'paginated' , 'api'];

    public const SELECTOR = ['static' , 'dynamic' , 'paginated' , 'authenticated' , 'api'];

    public const LINK_SELECTOR = ['seed','dynamic'];

    public static function all() : array
    {
        return [
            'all_steps' => self::ALL_STEP,
            'first_step' => self::FIRST_STEP,
            'second_step' => self::SECOND_STEP,
            'selector' => self::SELECTOR,
            'link_selector' => self::LINK_SELECTOR,
        ];
    }
    
}