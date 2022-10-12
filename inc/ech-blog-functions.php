<?php

/**
 * Include file - Used in ECH Blog plugin
 * 
 * Contains: 
 * 
 * 
 * 
 * @link       https://echealthcare.com/
 * @since      1.0.0 *
 * @package    ECH_Blog
 * 
 */

//require_once('load-template.php');

require_once('filters-functions.php');
require_once('cate-tags-list-functions.php');

function register_ech_blog_styles(){
	wp_register_style( 'ech_blog_style', plugins_url('/assets/css/ech-blog.css', __DIR__), false, '1.1.0', 'all');

	wp_register_script( 'ech_blog_pagination_js', plugins_url('/assets/js/ech-blog-pagination.js', __DIR__), array('jquery'), '1.0.0', true);
	wp_register_script( 'ech_blog_js', plugins_url('/assets/js/ech-blog.js', __DIR__), array('jquery'), '1.0.0', true);
}

function enqueue_ech_blog_styles() {
	wp_enqueue_style( 'ech_blog_style' );
	wp_enqueue_script( 'ech_blog_pagination_js');
	wp_enqueue_script( 'ech_blog_js');
}




$GLOBALS['api_domain'] = "https://globalcms-api.umhgp.com/";

$GLOBALS['ECHB_ppp'] = '';
$GLOBALS['ECHB_channel_id'] = '';
$GLOBALS['list_default_img'] = '/wp-content/uploads/2022/10/ec-logo.svg';

/****************************************
 * * Plugin main function
 ****************************************/
function ech_blog_fun($atts)
{

	$paraArr = shortcode_atts(array(
		'ppp' => 3,
		'channel_id' => 9,
		'brand_id' => 0,
		'dev_env' => false
	), $atts);

	$ppp = (int)$paraArr['ppp'];
	$channel_id = (int)$paraArr['channel_id'];
	$brand_id = (int)$paraArr['brand_id'];
	$dev_env = $paraArr['dev_env'];

	$GLOBALS['ECHB_ppp'] = $ppp;
	$GLOBALS['ECHB_channel_id'] = $channel_id;


	$api_args = array(
		'page_size'=>$ppp,
		'channel_id' => $channel_id,
		'brand_id' => $brand_id 
	);

	$api_link = gen_blog_list_api_link($api_args);

	$output = '';

	//$output .= '<div>' . $api_link . '</div>';
	if($dev_env) {
		$output .= '<div class="ech_blog_big_wrap" data-env="dev">';
	} else {
		$output .= '<div class="ech_blog_big_wrap">';
	}
	
	$output .= '<div class="echb_page_anchor"></div>'; // anchor
	/*********** FITLER ************/
	
	$output .= '<div class="ech_blog_filter_container">';

	$output .= ECHB_get_categories_list(); 
	/* $output .= ECHB_get_filter_tags();  */
	$output .= ECHB_get_filter_title(); 
	$output .= '<div class="filter_search_btn">'.blog_echolang([ 'Search', '搜尋', '搜寻']).'</div>';
	$output .= '</div>'; //ech_blog_filter_container
	/*********** (END) FITLER ************/
	
	
	/*********** POST LIST ************/
	$output .= '<div class="ech_blog_container" >';
	$get_blog_json = ECHB_curl_blog_json($api_link);
	$json_arr = json_decode($get_blog_json, true);

	/*** loading div ***/
	$output .= '<div class="loading_div"><p>'.blog_echolang(['Loading...','載入中...','载入中...']).'</p></div>';
	/*** (end) loading div ***/

	$output .= '<div class="all_posts_container" data-ppp="'.$ppp.'" data-channel="'.$channel_id .'" data-brand-id="'.$brand_id.'" data-category="" data-title="" data-tag="">';
		foreach ($json_arr['result'] as $post) {
			$output .= ECHB_load_post_card_template($post);
		}
	$output .= '</div>'; //all_posts_container


	/*** pagination ***/
	$total_posts = $json_arr['count'];
	$max_page = ceil($total_posts/$ppp);
	
	
	$output .= '<div class="ech_blog_pagination" data-current-page="1" data-max-page="'.$max_page.'" data-topage=""></div>';

	$output .= '</div>'; //ech_blog_container

	/*********** (END) POST LIST ************/

	$output .= '</div>'; //ech_blog_big_wrap

	return $output;
} // ech_blog_fun




/****************************************
 * Load Single Post Template
 ****************************************/
function ECHB_load_post_card_template($post) {
	$html = '';


	$thumbnail_arr_en = json_decode($post['en_blog_img'], true);
	$thumbnail_arr_zh = json_decode($post['tc_blog_img'], true);
	$thumbnail_arr_sc = json_decode($post['cn_blog_img'], true);


	// check ZH thumbnail if it is empty, empty EN and SC thumbnails will use ZH thumbnails becoz of blog_echolang()
	if($thumbnail_arr_zh[0] == "") {
		$thumbnail_arr_zh[0] = $GLOBALS['list_default_img'];
	}


	$publish_date = $post['product_time'];
	
	/***** CATEGORY *****/
	$cateArrEn = array();
	$cateArrZH = array();
	$cateArrSC = array();
	foreach($post['category'] as $label) {
		array_push($cateArrEn, array('type'=>'category', 'tag_id'=>$label['article_category_id'], 'tag_name'=> $label['en_name']) );
		array_push($cateArrZH, array('type'=>'category', 'tag_id'=>$label['article_category_id'], 'tag_name'=> $label['tc_name']));
		array_push($cateArrSC, array('type'=>'category', 'tag_id'=>$label['article_category_id'], 'tag_name'=> $label['cn_name']));
	}
	/***** (END) CATEGORY *****/
	

	/***** TAG *****/
	$tagsArrEN = array();
	$tagsArrZH = array();
	$tagsArrSC = array();
	foreach($post['label'] as $label) {
		array_push($tagsArrEN, array('type'=>'tag', 'tag_id'=>$label['label_id'], 'tag_name'=> $label['en_name']) );
		array_push($tagsArrZH, array('type'=>'tag', 'tag_id'=>$label['label_id'], 'tag_name'=> $label['tc_name']));
		array_push($tagsArrSC, array('type'=>'tag', 'tag_id'=>$label['label_id'], 'tag_name'=> $label['cn_name']));
	}
	/***** (END)TAG *****/


	$html .= '<div class="single_blog_post_card">';
	
	$html .= '<div class="post_thumb"><a href="'.site_url().'/health-blog-content/?article_id='.$post['forever_article_id'].'&post_version='.$post['forever_version'].'"><img src="' . blog_echolang([ $thumbnail_arr_en[0], $thumbnail_arr_zh[0], $thumbnail_arr_sc[0] ]) . '" /></a></div>';
	$html .= '<div class="post_title"><a href="'.site_url().'/health-blog-content/?article_id='.$post['forever_article_id'].'&post_version='.$post['forever_version'].'">' . blog_echolang([$post['en_title'], $post['tc_title'], $post['cn_title']]) . '</a></div>';
	$html .= '<div class="post_date">' . date('d/m/Y', $publish_date) . '</div>';	

	$html .= '<div class="post_cate"> <strong>'.blog_echolang(['Categories', '類別', '类别']).': </strong> ' . blog_echolang([ ECHB_apply_comma_from_array($cateArrEn) , ECHB_apply_comma_from_array($cateArrZH), ECHB_apply_comma_from_array($cateArrSC) ]) . '</div>';

	$html .= '<div class="post_tag"> <strong>'.blog_echolang(['Tags', '標籤', '标签']).': </strong> ' . blog_echolang([ ECHB_apply_comma_from_array($tagsArrEN) , ECHB_apply_comma_from_array($tagsArrZH), ECHB_apply_comma_from_array($tagsArrSC) ]) . '</div>';


	$html .= '</div>'; //single_blog_post_card

	return $html;
}




/****************************************
 * Load more posts
 ****************************************/
function ECHB_load_more_posts() {
	$ppp = $_POST['ppp'];
	$toPage = $_POST['toPage'];
	$brand_id = $_POST['brand_id'];
	$filterTitle = $_POST['filterTitle'];
	$filterCate = $_POST['filterCate'];
	$filterTag = $_POST['filterTag'];
	

	$api_args = array(
		'page_size'=>$ppp,
		'page' => $toPage,
		'brand_id' => $brand_id,
		'title' => $filterTitle,
		'cate_id' => $filterCate,
		'tag_id' => $filterTag
	);
	$api_link = gen_blog_list_api_link($api_args);

	$get_blog_json = ECHB_curl_blog_json($api_link);
	$json_arr = json_decode($get_blog_json, true);
	
	$html = '';
	$max_page = '';

	if(isset($json_arr['result']) && $json_arr['count'] != 0 ) {
		$total_posts = $json_arr['count'];
        $max_page = round($total_posts/$ppp, 0);

        foreach ($json_arr['result'] as $post) {
            $html .= ECHB_load_post_card_template($post);
        }
    } else {
        $html .= blog_echolang(['No posts ...' , '沒有文章', '没有文章']);
    }

	echo json_encode(array('html'=>$html, 'max_page' => $max_page), JSON_UNESCAPED_SLASHES);

	wp_die();
}





/****************************************
 * Filter and merge value and return a full API Blog List link. 
 * Array key: page, page_size, channel_id, get_type, title, content, label_name, publisher_name
 ****************************************/
function gen_blog_list_api_link(array $args){
	// /v1/api/blog_article_list?page=1&page_size=3&channel_id=9&get_type=1&title&content&label_name&publisher_name
	$full_api = $GLOBALS['api_domain'] . '/v1/api/blog_article_list?';

	if(!empty($args['page'])) {
		$full_api .= 'page=' . $args['page'];
	} else {
		$full_api .= 'page=1';
	}


	if(!empty($args['page_size'])) {
		$full_api .= '&';
		$full_api .= 'page_size=' . $args['page_size'];
	} else {
		$full_api .= '&';
		$full_api .= 'page_size=9';
	}


	if(!empty($args['channel_id'])) {
		$full_api .= '&';
		$full_api .= 'channel_id=' . $args['channel_id'];
	} else {
		$full_api .= '&';
		$full_api .= 'channel_id=9';
	}


	if(!empty($args['get_type'])) {
		$full_api .= '&';
		$full_api .= 'get_type=' . $args['get_type'];
	} else {
		$full_api .= '&';
		$full_api .= 'get_type=1';
	}


	if(!empty($args['title'])) {
		$full_api .= '&';
		$full_api .= 'title=' . $args['title'];
	} 

	if(!empty($args['content'])) {
		$full_api .= '&';
		$full_api .= 'content=' . $args['content'];
	}

	if(!empty($args['cate_id'])) {
		$full_api .= '&';
		$full_api .= 'article_category_id=' . $args['cate_id'];
	}

	if(!empty($args['tag_id'])) {
		$full_api .= '&';
		$full_api .= 'label_id=' . $args['tag_id'];
	}

	
	if(!empty($args['publisher_name'])) {
		$full_api .= '&';
		$full_api .= 'publisher_name=' . $args['publisher_name'];
	}

	if(!empty($args['brand_id'])) {
		$full_api .= '&';
		$full_api .= 'brand_id=' . $args['brand_id'];
	}


	return $full_api;
}


/****************************************
 * Filter and merge value and return a full API Post Content link. 
 * Array key: article_id, channel_id
 ****************************************/
function gen_post_api_link(array $args){
	$full_api = $GLOBALS['api_domain'] . '/v1/api/article_detail/?blog=1';

	if(!empty($args['article_id'])) {
		$full_api .= '&';
		$full_api .= 'article_id=' . $args['article_id'];
	} 

	if(!empty($args['version'])) {
		$full_api .= '&';
		$full_api .= 'version=' . $args['version'];
	} 

	if(!empty($args['channel_id'])) {
		$full_api .= '&';
		$full_api .= 'channel_id=' . $args['channel_id'];
	} else {
		$full_api .= '&';
		$full_api .= 'channel_id=9';
	}

	return $full_api;
}




/****************************************
 * Get Blog JSON Using API
 ****************************************/
function ECHB_curl_blog_json($api_link) {
	$ch = curl_init();

	$api_headers = array(
		'accept: application/json',
		'version: v1',
	);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $api_headers);
	curl_setopt($ch, CURLOPT_URL, $api_link);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	curl_close($ch);

	return $result;
}




/****************************************
 * DISPLAY SPECIFIC LANGUAGE
 ****************************************/
function blog_echolang($stringArr) {
	global $TRP_LANGUAGE;

	switch ($TRP_LANGUAGE) {
		case 'zh_HK':
			$langString = $stringArr[1];
			break;
		case 'zh_CN':
			$langString = $stringArr[2];
			break;
		default:
			$langString = $stringArr[0];
	}

	if(empty($langString) || $langString == '' || $langString == null) {
		$langString = $stringArr[1]; //zh_HK
	}

	return $langString;

}
/********** (END)DISPLAY SPECIFIC LANGUAGE **********/





function ECHB_displayPostContent($contentLangArr) {

	global $TRP_LANGUAGE;

	switch ($TRP_LANGUAGE) {
		case 'zh_HK':
			$contentArr = $contentLangArr[1];
			break;
		case 'zh_CN':
			$contentArr = $contentLangArr[2];
			break;
		default:
			$contentArr = $contentLangArr[0];
	}

	

	$html = '';
	foreach($contentArr as $key=>$data ) {
		// If empty value in EN and SC, display ZH value
		if ($data['title'] == '') {
			$data['title'] = $contentLangArr[1][$key]['title'];
		}
		if ($data['content'] == '') {
			$data['content'] = $contentLangArr[1][$key]['content'];
		}		
		if ($data['desktop_img'] == '') {
			$data['desktop_img'] = $contentLangArr[1][$key]['desktop_img'];
		}
		if ($data['mobile_img'] == '') {
			$data['mobile_img'] = $contentLangArr[1][$key]['mobile_img'];
		}

		// display content
		$html .= '<div class="ECHB_p_section">';
		$html .= '<h2>'. $data['title'] . '</h2>';
		$html .= '<p>'. $data['content'] . '</p>';
		if($data['desktop_img'] != '') {
			$html .= '<img src="'. $data['desktop_img'] .'" class="hidden_b_w1024" />';
		}

		if($data['mobile_img'] != '') {
			$html .= '<img src="'. $data['mobile_img'] .'" class="show_b_w1024" />';
		}
		$html .= '</div>'; //ECHB_p_section
	}

	return $html;
	
}



/****************************************
 * Sort Content Paragraphs. 
 * This function is used to get the corresponding ZH values if empty values in EN and SC
 ****************************************/
function ECHB_sortContentArr($contentArr) {
	// Sort paragraphs by the value 'sort'. Sort empty 'sort' value at the end of the array
	usort($contentArr, function($a, $b) {
        if ($a['sort'] == "") return 1;
        if ($b['sort'] == "") return -1;
        return $a['sort'] - $b['sort'];
    });	

	// find FOOTER array and temporary remove it from content array. 
	foreach($contentArr as $k=>$pArr) {
		if($pArr['part'] == 'FOOTER') {
			$footerArr = $pArr;
			unset($contentArr[$k]);
		}
	}
	
	// re-index the content array
	$contentArr = array_values($contentArr);

	// if FOOTER array exist, add it back at the end of content array
	if(isset($footerArr)) {
		array_push($contentArr, $footerArr);
	}
	

	return $contentArr;
}



/****************************************
 * Blog List - categories / tags comma separated list from array
 * This function is used to create a comma sparated list from an array. It is used on API Blog list categories / tags display
 ****************************************/
function ECHB_apply_comma_from_array($langArr) {
	$prefix = $commaList = '';
	$type = '';

	foreach($langArr as $itemArr) {
		if($itemArr['type'] == 'tag') {
			$type = 'tag_id=';
		} else {
			$type = 'cate_id=';
		}
		$commaList .= $prefix . '<a href="'.site_url().'/health-blog-category-tag-list/?'.$type.$itemArr['tag_id'].'">' . $itemArr['tag_name']. '</a>';
		$prefix = ", ";
	}

	return $commaList;
}



