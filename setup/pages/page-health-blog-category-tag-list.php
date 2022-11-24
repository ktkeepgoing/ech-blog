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
if( (!isset($_GET['cate_id']) && !isset($_GET['tag_id'])) || (empty($_GET['cate_id']) && empty($_GET['tag_id'])) ) {
    header('Location:' . site_url() . '/health-blog');
    exit;
}




$ppp = 12;
$env = '';


/**
 * Get JSON from API
 */
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
    'page_size' => $ppp,
    'cate_id' => $cate_id,
    'tag_id' => $tag_id,
    'brand_id' => $brand_id
);

$api_link = gen_blog_list_api_link($args);

$get_cate_tag_json = ECHB_curl_blog_json($api_link);
$json_arr = json_decode($get_cate_tag_json, true);


/**
 * Redirect to blog list page if cate_id is invalid
 */

if (!isset($json_arr['count']) || $json_arr['count'] == 0) {
    header('Location:' . site_url() . '/health-blog');    
    exit;
}


global $wp;


add_action('wp_head', 'cu_add_to_head', 7);
function cu_add_to_head()
{
    global $TRP_LANGUAGE;

    wp_enqueue_style('ech_blog_post_content_css', get_stylesheet_directory_uri() . "/assets/css/ech-blog-single-post.css");
    wp_enqueue_style('ech_blog_cate_tags_css', get_stylesheet_directory_uri() . "/assets/css/ech-blog-cate-tags-list.css");

    /*
    if ($TRP_LANGUAGE == 'zh_HK' || $TRP_LANGUAGE == 'zh_CN') {
        wp_enqueue_style('ech_blog_post_content_ZH_css', get_stylesheet_directory_uri() . "/assets/css/ech-blog-single-post-zh.css");
    }
    */
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
        $title_type = '';
        $title_name = '';
        $brand_id = 0;
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

        if(isset($_GET['brand_id'])) {
            $brand_id = $_GET['brand_id'];
        }
    ?>



    <div class="ech_blog_cate_tags_all_wrap" data-env="<?=$env?>">

        <div class="sp_breadcrumb">
            <div><a href="<?= site_url() ?>"><?= blog_echolang(['Home', '主頁', '主页']) ?></a> > <a href="<?= site_url() . '/api-blog/' ?>"><?= blog_echolang(['Health Blog', '健康資訊', '健康资讯']) ?></a> > <?=$title_type.': '.$title_name ?> </div>
        </div> <!-- sp_breadcrumb -->

        <div class="echb_page_anchor"></div>

        <div class="ECHB_back_to_blog_list"><a href="<?=site_url()?>/health-blog/"> <?= blog_echolang(['Back to health blog', '返回健康專欄', '返回健康专栏']) ?></a></div>
        <div class="ECHB_search_title">
            <p><span><?=$title_type?>: </span><?=$title_name?> </p>
        </div>

        <div class="ech_blog_container">
            <div class="loading_div"><p><?=blog_echolang(['Loading...','載入中...','载入中...'])?></p></div>

            <div class="all_posts_container" data-ppp="<?=$ppp?>" data-channel="<?=$channel_id?>" data-category="<?=$cate_id?>" data-title="" data-tag="<?=$tag_id?>" data-brand-id="<?=$brand_id?>">
                <?php foreach($json_arr['result'] as $post): ?>
                <?=ECHB_load_post_card_template($post, $brand_id)?>
                <?php endforeach; ?>
            </div> <!-- all_posts_container -->

            <?php 
                /*** pagination ***/
                $total_posts = $json_arr['count'];
                $max_page = ceil($total_posts/$ppp);
            ?>
            <div class="ech_blog_pagination" data-current-page="1" data-max-page="<?=$max_page?>" data-topage="" data-brand-id="<?=$brand_id?>"></div>

        </div> <!-- ech_blog_container -->


        

    </div> <!-- ech_blog_cate_tags_all_wrap -->



</div><!-- #primary -->

<?php if (astra_page_layout() == 'right-sidebar') : ?>

    <?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>