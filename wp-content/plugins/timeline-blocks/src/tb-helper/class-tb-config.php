<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * TB Config.
 *
 * @package TB
 */
if (!class_exists('TB_Config')) {

    /**
     * Class TB_Config.
     */
    class TB_Config {

        /**
         * Block Attributes
         *
         * @var block_attributes
         */
        public static $block_attributes = null;

        /**
         * Get Widget List.
         *
         * @since 0.0.1
         *
         * @return array The Widget List.
         */
        public static function get_block_attributes() {

            if (null === self::$block_attributes) {
                self::$block_attributes = array(
                    'timeline-blocks/tb-timeline-blocks' => array(
                        'slug' => '',
                        'title' => __('Timeline Block', 'timeline-blocks'),
                        'description' => __('This block fetches the blog posts you may have on your website and displays them in a timeline template.', 'timeline-blocks'),
                        'default' => true,
                        'attributes' => array(
                            'categories' => '',
                            'className' => '',
                            'postsToShow' => '',
                            'displayPostDate' => '',
                            'displayPostExcerpt' => '',
                            'wordsExcerpt' => '',
                            'displayPostAuthor' => '',
                            'displayPostTag' => '',
                            'displayPostCategory' => '',
                            'displayPostImage' => '',
                            'displayPostLink' => '',
                            'displayPostComments' => '',
                            'displayPostSocialshare' => '',
                            'align' => '',
                            'width' => '',
                            'order' => '',
                            'orderBy' => '',
                            'imageCrop'  => '',
                            'layoutcount' => '',
                            'readMoreText' => '',
                            'titleTag' => '',
                            'titlefontSize' => '',
                            'titleFontFamily' => '',
                            'titleFontWeight' => '',
                            'titleFontSubset' => '',
                            'postmetafontSize' => '',
                            'postexcerptfontSize' => '',
                            'postctafontSize' => '',
                            'metaFontFamily' => '',
                            'metaFontSubset' => '',
                            'metafontWeight' => '',
                            'excerptFontFamily' => '',
                            'excerptFontWeight' => '',
                            'excerptFontSubset' => '',
                            'ctaFontFamily' => '',
                            'ctaFontSubset' => '',
                            'ctafontWeight' => '',
                            'socialSharefontSize' => '',
                            'readmoreView' => '',
                            'belowTitleSpace' => '',
                            'belowImageSpace' => '',
                            'belowexerptSpace' => '',
                            'belowctaSpace' => '',
                            'innerSpace' => '',
                            'boxbgColor' => '',
                            'titleColor' => '',
                            'postmetaColor' => '',
                            'postexcerptColor' => '',
                            'postctaColor' => '#abb8c3',
                            'socialShareColor' => '',
                            'designtwoboxbgColor' => '',
                            'timelineBgColor' => '',
                            'timelineFgColor' => '',
                            'readmoreBgColor' => '#282828',
                        ),
                    ),
                );
            }
            return self::$block_attributes;
        }

    }

}
