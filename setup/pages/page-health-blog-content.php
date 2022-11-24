<?php

/**
 * Template Name: ECH Blog Single Post Template
 */
/**
 * ! Plugin ECH Blog is being used in this template. Activate the plugin to use this template.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Redirect to blog list page if no article_id is passed
 */
if( (!isset($_GET['article_id']) && !isset($_GET['post_version'])) || (empty($_GET['article_id']) && empty($_GET['post_version'])) ) {
    header('Location:' . site_url() . '/health-blog');
    exit;
}

/**
 * Get JSON from API
 */
$articleID  = $_GET['article_id'];
$postVersion  = $_GET['post_version'];
$scAttr_brand_id  = $_GET['scAttr_brand_id'];

$args = array(
    'article_id' => $articleID,
    'version' => $postVersion
);
$api_link = gen_post_api_link($args);

$get_post_json = ECHB_curl_blog_json($api_link);
$json_arr = json_decode($get_post_json, true);

/**
 * Redirect to blog list page if article_id is invalid
 */

if (!isset($json_arr['result_code']) || $json_arr['result_code'] != 0) {
    header('Location:' . site_url() . '/health-blog');
    exit;
}


/***** RANK MATH - MAGE DATA ******/
add_filter( 'rank_math/frontend/title', function( $title ) {
    $title = '';
    $current_url = $_SERVER['REQUEST_URI'];
	$url_arr = parse_url($current_url);

    if(str_contains($url_arr['path'], '/health-blog-content')) {
        $title = 'Single Post';
    }
    
	return $title;
});

/***** (END)RANK MATH - MAGE DATA ******/





global $wp;

add_action('wp_head', 'cu_add_to_head', 7);
function cu_add_to_head()
{
    global $TRP_LANGUAGE;

    wp_enqueue_style('ech_blog_post_content_css', get_stylesheet_directory_uri() . "/assets/css/ech-blog-single-post.css");

    if ($TRP_LANGUAGE == 'zh_HK' || $TRP_LANGUAGE == 'zh_CN') {
        wp_enqueue_style('ech_blog_post_content_ZH_css', get_stylesheet_directory_uri() . "/assets/css/ech-blog-single-post-zh.css");
    }
}

get_header();
?>



<?php if (astra_page_layout() == 'left-sidebar') : ?>
    <?php get_sidebar(); ?>
<?php endif ?>

<div id="primary" <?php astra_primary_class(); ?>>
    <?php astra_primary_content_top(); ?>
    <?php astra_content_page_loop(); ?>
    <?php astra_primary_content_bottom(); ?>


    <?php

    $post = $json_arr['result'];

    $contentMainImg_en = json_decode($post['en_blog_img'], true);
    $contentMainImg_zh = json_decode($post['tc_blog_img'], true);
    $contentMainImg_sc = json_decode($post['cn_blog_img'], true);


    $contentEN = json_decode($post['en_blog_content'], true);
    $contentZH = json_decode($post['tc_blog_content'], true);
    $contentSC = json_decode($post['cn_blog_content'], true);



    // Sort paragraphs by value 'sort' using function in the plugin
    $contentEN = ECHB_sortContentArr($contentEN);
    $contentZH = ECHB_sortContentArr($contentZH);
    $contentSC = ECHB_sortContentArr($contentSC);
    
    ?>




    <div class="all_single_post_wrap">
        <?php $post_title = blog_echolang([$post['en_title'], $post['tc_title'], $post['cn_title']]);  ?>

        <div class="sp_breadcrumb">
            <div><a href="<?= site_url() ?>"><?= blog_echolang(['Home', '主頁', '主页']) ?></a> > <a href="<?= site_url() . '/api-blog/' ?>"><?= blog_echolang(['Health Blog', '健康資訊', '健康资讯']) ?></a> > <?= $post_title ?> </div>
        </div> <!-- sp_breadcrumb -->

        <div class="single_post_container">
            <div class="post_container">
                <div class="post_title"><h1><?= $post_title ?></h1></div>

                <div class="post_info">
                    <div class="post_date"><?= date('d/m/Y', $post['product_time']) ?></div>
                    <?php
                    /***** TAG *****/
                    $tagsArrEN = array();
                    $tagsArrZH = array();
                    $tagsArrSC = array();
                    foreach($post['label'] as $label) {
                        array_push($tagsArrEN, array('type'=>'tag', 'tag_id'=>$label['label_id'], 'tag_name'=> $label['label_en_name']) );
                        array_push($tagsArrZH, array('type'=>'tag', 'tag_id'=>$label['label_id'], 'tag_name'=> $label['label_tc_name']));
                        array_push($tagsArrSC, array('type'=>'tag', 'tag_id'=>$label['label_id'], 'tag_name'=> $label['label_cn_name']));
                    }
                    /***** (END)TAG *****/
                    ?>

                    <?php
                        /***** SHARE TEXT *****/
                        $shareTxt = blog_echolang([$post['en_share'], $post['tc_share'], $post['cn_share']]);
                        if($shareTxt == '' || $shareTxt == null) {                                                
                            $shareTxt = $post_title;
                        }
                        $shareTxt = str_replace(' ', '_', $shareTxt);
                        $shareTxt = preg_replace('~[^\p{L}\p{N}\_]+~u', '', $shareTxt);
                        /***** (END)SHARE TEXT *****/
                    ?>
                    <div class="post_tag"><?= blog_echolang(['Topics', '標籤', '标签']) ?>: <?= blog_echolang([ ECHB_apply_comma_from_array($tagsArrEN, $brand_id) , ECHB_apply_comma_from_array($tagsArrZH, $brand_id), ECHB_apply_comma_from_array($tagsArrSC, $brand_id) ]); ?></div>
                    <div class="post_share">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= home_url(add_query_arg($_GET, $wp->request)) ?>" target="_blank"><img src="<?= site_url() ?>/wp-content/uploads/2022/06/author-fb.svg" alt="" class="post_fb"></a>
                        
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= home_url(add_query_arg($_GET, $wp->request)) ?>" target="_blank"><img src="<?= site_url() ?>/wp-content/uploads/2022/06/author-linkedin.svg" alt="" class="post_llinkedin"></a>

                        <a href="https://api.whatsapp.com/send?text=<?=$shareTxt?>%20-%20<?= home_url(add_query_arg($_GET, $wp->request)) ?>" data-action="share/whatsapp/share" target="_blank"><img src="<?= site_url() ?>/wp-content/uploads/2022/09/author_wtsapp.svg" alt="" class="post_wtsapp"></a>

                    </div>
                </div>

                <div class="post_content">
                    
                    <div class="content_main_img">
                        <img src="<?= blog_echolang([$contentMainImg_en[1], $contentMainImg_zh[1], $contentMainImg_sc[1]]); ?>" alt="" class="hidden_b_w1024">
                        <img src="<?= blog_echolang([$contentMainImg_en[3], $contentMainImg_zh[3], $contentMainImg_sc[3]]); ?>" alt="" class="show_b_w1024">
                    </div> <!-- content_main_img -->


                    <div class="content">
                        <?= ECHB_displayPostContent([$contentEN, $contentZH, $contentSC]); ?>
                    </div>

                    
                    <div class="post_source">
                        <?php if ($post['blog_published_sources'] == 1) : // dr source 
                        ?>

                            <?php foreach ($post['doctors'] as $dr) : ?>
                                <?php
                                    // Dr Name
                                    $dr_nameEN = $dr['en_salutation'].' '.$dr['en_name'];
                                    $dr_nameZH = $dr['tc_name'].$dr['tc_salutation'];
                                    $dr_nameSC = $dr['cn_name'].$dr['cn_salutation'];

                                    // specialist fields
                                    $spArrEN = array();
                                    $spArrZH = array();
                                    $spArrSC = array();

                                    foreach ($dr['specialty_list'] as $spList) {
                                        array_push($spArrEN, $spList['en_name']);
                                        array_push($spArrZH, $spList['tc_name']);
                                        array_push($spArrSC, $spList['cn_name']);
                                    }
                                ?>
                                <div class="dr_source">
                                    <div class="dr_profile"><img src="<?= $dr['avatar'] ?>" alt=""></div>
                                    <div class="dr_info">
                                        <div class="dr_name"><?= blog_echolang([$dr_nameEN, $dr_nameZH, $dr_nameSC]) ?></div>
                                        <div class="dr_field"><?= blog_echolang([implode(', ', $spArrEN), implode(', ', $spArrZH), implode(', ', $spArrSC)]); ?> </div> <!-- dr_field -->
                                        <?php if ($post['tc_blog_url'] != '') : ?>
                                            <div class="dr_booking"><a href="<?= blog_echolang([$post['en_blog_url'], $post['tc_blog_url'], $post['cn_blog_url']]) ?>" target="_blank"><?= blog_echolang(['Book Appointment', '預約醫生', '预约医生']) ?></a></div>
                                        <?php endif; ?>
                                    </div>
                                </div> <!-- dr_source -->
                            <?php endforeach; ?>

                        <?php else : ?>
                            <div class="media_source"><?= blog_echolang(['Source', '來源', '来源']) ?>: <a href="<?= blog_echolang([$post['en_blog_url'], $post['tc_blog_url'], $post['cn_blog_url']]) ?>" target="_blank"><?= blog_echolang([$post['en_issuer'], $post['tc_issuer'], $post['cn_issuer']]) ?></a></div>
                        <?php endif; ?>
                    </div>
                </div>

            </div> <!-- post_container -->

            <div class="brand_container">
                <div class="inner_brand_container">
                    <p><?= blog_echolang(['Related Brands', '相關品牌', '相关品牌']) ?></p>
                    <?php foreach ($post['brand'] as $brand) : ?>
                        <div class="single_brand_container" data-brandid="<?=$brand['forever_brand_id']?>">
                            <?php
                            $brandImgEN = json_decode($brand['en_picture'], true);
                            $brandImgZH = json_decode($brand['tc_picture'], true);
                            $brandImgSC = json_decode($brand['cn_picture'], true);

                            if ($brandImgZH[0] != '') :
                            ?>
                                <div class="brand_img">
                                    <img src="<?= blog_echolang([$brandImgEN[0], $brandImgZH[0], $brandImgSC[0]]) ?>" alt="<?= blog_echolang([$brand['en_name'], $brand['tc_name'], $brand['cn_name']]) ?>">
                                </div>
                            <?php endif; ?>
                            <div class="brand_name"><?= blog_echolang([$brand['en_name'], $brand['tc_name'], $brand['cn_name']]) ?></div>

                            <?php if ($brand['brand_website_url'] != null || $brand['brand_website_url'] != ''): ?>
                            <div class="brand_learn_more"><a href="<?=$brand['brand_website_url']?>" target="_blank"><?= blog_echolang(['Learn More', '了解更多', '了解更多']) ?></a></div>
                            <?php endif; ?>
                        </div> <!-- single_brand_container -->
                    <?php endforeach; ?>
                </div>
            </div> <!-- brand_container -->
        </div> <!-- single_post_container -->


        <?php if(!empty($post['similarity_article'])) :?>
            <div class="related_article_wrap">
                <h3><?= blog_echolang(['Related Articles','相關文章','相关文章']); ?></h3>
                <div class="related_articles_container">
                    <?php foreach($post['similarity_article'] as $related): ?>        
                        <?= ECHB_load_post_card_template($related, $scAttr_brand_id); ?>
                    <?php endforeach; ?>    
                </div> <!-- related_articles_container-->
            </div>
        <?php endif; ?>


    </div> <!-- all_single_post_wrap -->


    
    


    

</div><!-- #primary -->

<?php if (astra_page_layout() == 'right-sidebar') : ?>

    <?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>