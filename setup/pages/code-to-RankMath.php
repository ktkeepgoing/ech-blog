<?php

/**
 * Below code section should be imported in "rank-math.php" file
 * More details below on how to create "rank-math.php" file
 * https://rankmath.com/kb/filters-hooks-api-developer/ 
 */




/***** RANK MATH - MAGE DATA ******/


/*************************************************************************
 * Update single post page and category/tag list post page meta title 
 *************************************************************************/
add_filter( 'rank_math/frontend/title', function( $title ) {
    $current_url = $_SERVER['REQUEST_URI'];
    $url_arr = parse_url($current_url);

    if (str_contains($url_arr['path'], '/health-blog-content')) {
        $articleID  = $_GET['article_id'];
        $postVersion  = $_GET['post_version'];

        $args = array(
            'article_id' => $articleID,
            'version' => $postVersion
        );
        $api_link = gen_post_api_link($args);

        $get_post_json = ECHB_curl_blog_json($api_link);
        $json_arr = json_decode($get_post_json, true);
        $post = $json_arr['result'];


        $title = blog_echolang([$post['en_title'] . ' - BRAND ENG NAME', $post['tc_title'] . ' - 品牌繁體', $post['cn_title'] . ' - 品牌簡體']);
    } // if health-blog-content page

    
    if (str_contains($url_arr['path'], '/health-blog-category-tag-list')) {
        if(isset($_GET['cate_id'])) {
            $cate_id  = $_GET['cate_id'];
        }
        if(isset($_GET['tag_id'])) {
            $tag_id  = $_GET['tag_id'];
        }
        
        if(isset($_GET['brand_id'])) {
            $brand_id  = $_GET['brand_id'];
        }
        $args = array(
            'cate_id' => $cate_id,
            'tag_id' => $tag_id,
            'brand_id' => $brand_id
        );
        $api_link = gen_blog_list_api_link($args);

        $get_cate_tag_json = ECHB_curl_blog_json($api_link);
        $json_arr = json_decode($get_cate_tag_json, true);

        $title_type = '';
        $title_name = '';
        if(isset($_GET['cate_id'])) {
            // Get Category Names
            $getCateName_json = ECHB_get_category_name($cate_id);
            $cateNameArr = json_decode($getCateName_json, true);

            $title_type = blog_echolang(['Category', '類別', '类别']);
            $title_name = blog_echolang([ $cateNameArr['en'], $cateNameArr['zh'], $cateNameArr['sc']]);
        }

        if(isset($_GET['tag_id'])) {
            // Get Tag Name
            $getTagName_json = ECHB_get_tag_name($tag_id);
            $tagNameArr = json_decode($getTagName_json, true);

            $title_type = blog_echolang(['Tag', '標籤', '标签']);
            $title_name = blog_echolang([ $tagNameArr['en'], $tagNameArr['zh'], $tagNameArr['sc']]);
        }

        $title = $title_type . ': ' . $title_name . ' - ' . blog_echolang(['BRAND ENG NAME','品牌繁體','品牌簡體']);
    } // if health-blog-category-tag-list page
    
	return $title;
});




/***********************************************
 * Update single post page meta description 
 ***********************************************/
add_filter('rank_math/frontend/description', function ($description) {
    $current_url = $_SERVER['REQUEST_URI'];
    $url_arr = parse_url($current_url);

    if (str_contains($url_arr['path'], '/health-blog-content')) {
        $articleID  = $_GET['article_id'];
        $postVersion  = $_GET['post_version'];

        $args = array(
            'article_id' => $articleID,
            'version' => $postVersion
        );
        $api_link = gen_post_api_link($args);

        $get_post_json = ECHB_curl_blog_json($api_link);
        $json_arr = json_decode($get_post_json, true);
        $post = $json_arr['result'];

        $description = blog_echolang([$post['en_blog_short_description'], $post['tc_blog_short_description'], $post['cn_blog_short_description']]);

        if($description == 'HEALTH BLOG') {
            $description = '';
        }
    }
    return $description;
});

/***********************************************
 * Update single post page canonical url
 ***********************************************/
add_filter( 'rank_math/frontend/canonical', function( $canonical ) {
	$current_url = $_SERVER['REQUEST_URI'];
    $url_arr = parse_url($current_url);
    if (strpos($url_arr['path'], '/health-blog-content')) {
    //if (str_contains($url_arr['path'], '/health-blog-content')) {
        $base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
        $url = $base_url . $_SERVER["REQUEST_URI"];
        $canonical = $url;
    }
    return $canonical;
});


/***********************************************************
 * Update single post page social media share thumbnail
 ***********************************************************/
add_filter("rank_math/opengraph/facebook/image", function ($attachment_url) {
    $current_url = $_SERVER['REQUEST_URI'];
    $url_arr = parse_url($current_url);

    if (str_contains($url_arr['path'], '/health-blog-content')) {
        $articleID  = $_GET['article_id'];
        $postVersion  = $_GET['post_version'];

        $args = array(
            'article_id' => $articleID,
            'version' => $postVersion
        );
        $api_link = gen_post_api_link($args);

        $get_post_json = ECHB_curl_blog_json($api_link);
        $json_arr = json_decode($get_post_json, true);
        $post = $json_arr['result'];

        $contentMainImg_en = json_decode($post['en_blog_img'], true);
        $contentMainImg_zh = json_decode($post['tc_blog_img'], true);
        $contentMainImg_sc = json_decode($post['cn_blog_img'], true);

        $attachment_url = blog_echolang([$contentMainImg_en[3], $contentMainImg_zh[3], $contentMainImg_sc[3]]); 
    }
    return $attachment_url;
});


/***** (END)RANK MATH - MAGE DATA ******/