<?php



/****************************************
 * Get all categories from API
 ****************************************/
function ECHB_get_categories_list($show_cate) {
    $show_cateArr = array();
    if($show_cate != '') {
        $show_cateArr = explode(",", $show_cate);
    }
    
    
    $full_api = $GLOBALS['api_domain'] . '/v1/api/article_categories_list?get_type=1&page=1&page_size=50&channel_id=9';
    $get_cateList_json = ECHB_curl_blog_json($full_api);
    $json_arr = json_decode($get_cateList_json, true);


    $html = '';
    
    // Desktop    
    $html .= '<div class="D_categories_filter_container">';
    $html .= '<div data-catefilterid="" class="D_cate_filter active">'.blog_echolang(['All Categories','全部類別','全部类别']).'</div>';

    if (empty($show_cateArr)) {
        foreach($json_arr['result'] as $category) {
            $html .= '<div data-catefilterid="'.$category['article_category_id'].'" class="D_cate_filter">'.blog_echolang([$category['en_name'], $category['tc_name'], $category['cn_name'] ]).'</div>';
        }
    } else {
        foreach($show_cateArr as $show_cate) {
            foreach($json_arr['result'] as $key=>$category) {
                if(in_array($show_cate, $category)) {
                    $html .= '<div data-catefilterid="'.$category['article_category_id'].'" class="D_cate_filter">'.blog_echolang([$category['en_name'], $category['tc_name'], $category['cn_name'] ]).'</div>';
                }
            }
        }
    }
    
    $html .= '</div>'; //D_categories_filter_container

    // Mobile 
    $html .= '<div class="M_categories_filter_container">';
    $html .= '<select name="categories_filter_M" id="categories_filter_M" class="categories_filter_M">';
        $html .= '<option value="">'.blog_echolang(['All Categories','全部類別','全部类别']).'</option>';

        if (empty($show_cateArr)) {
            foreach($json_arr['result'] as $category) {
                $html .= '<option value="'.$category['article_category_id'].'">'.blog_echolang([$category['en_name'], $category['tc_name'], $category['cn_name'] ]).'</option>';
            }
        } else {
            foreach($show_cateArr as $show_cate) {
                foreach($json_arr['result'] as $key=>$category) {
                    if(in_array($show_cate, $category)) {
                        $html .= '<option value="'.$category['article_category_id'].'">'.blog_echolang([$category['en_name'], $category['tc_name'], $category['cn_name'] ]).'</option>';
                    }
                }
            }
        }

        
        
    $html .= '</select>'; 
    $html .= '</div>'; //M_categories_filter_container

    return $html;
}


function ECHB_get_filter_tags() {
    $html = '';
    $html .= '<div class="categories_filter">';
    $html .= '<select name="tag_filter" id="tag_filter" class="tag_filter">';
        $html .= '<option value="">'.blog_echolang(['All Tags','全部標簽','全部标签']).'</option>';
    $html .= '</select>'; 
    $html .= '</div>'; //categories_filter

    return $html;
}





function ECHB_get_filter_title() {
    $html = '';
    $html .= '<div class="title_filter">';
    $html .= '<input type="text" name="title_filter" class="title_filter" id="title_filter" placeholder="'.blog_echolang(['Filter by title','搜尋標題','搜寻标题']).'" />';
    $html .= '</div>'; //tags_filter

    return $html;
}



function ECHB_get_filter_content() {
    $html = '';
    $html .= '<div class="content_filter">';
    $html .= '<input type="text" name="content_filter" class="content_filter" id="content_filter" placeholder="'.blog_echolang(['Filter by content','搜尋內容','搜寻内容']).'" />';
    $html .= '</div>'; //tags_filter

    return $html;
}








/****************************************
 * Filter blog posts
 * filter: category, title
 ****************************************/
function ECHB_filter_blog_list() {
	$ppp = $_POST['ppp'];
	$brand_id = $_POST['brand_id'];
	$filterCate = $_POST['filterCate'];
	$filterTitle = $_POST['filterTitle'];

	$api_args = array(
		'page_size'=>$ppp,
        'brand_id' => $brand_id,
        'cate_id' => $filterCate,
        'title' => $filterTitle
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
            $html .= ECHB_load_post_card_template($post, $brand_id);
        }
    } else {
        $html .= blog_echolang(['No posts ...' , '沒有文章', '没有文章']);
    }
	
	echo json_encode(array('html'=>$html, 'max_page' => $max_page, 'api' => $api_link), JSON_UNESCAPED_SLASHES);

	wp_die();
}


