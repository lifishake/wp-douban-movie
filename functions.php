<?php

function wpd_install(){
    $thumb_path = ABSPATH . "douban_cache/";
    if (file_exists ($thumb_path)) {
        if (! is_writeable ( $thumb_path )) {
            @chmod ( $thumb_path, '511' );
        }
    } else {
        @mkdir ( $thumb_path, '511', true );
    }
}

function movie_detail( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'id' => ''
    ),$atts));
    $id = trim($id);
    $movieids =  explode(',', $id);
    foreach ( $movieids as $movieid ){
        $output .= display_movie_detail($movieid);
    }
    return $output;
}

add_shortcode('douban', 'movie_detail');
add_filter('comment_text', 'do_shortcode');

function wpd_get_post_image($post_id) {
    $content         =  get_post_field('post_content', $post_id);
    $content         = apply_filters('the_content', $content);
    $defaltthubmnail = get_template_directory_uri() . '/build/images/default.jpeg';
    preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
    $n = count($strResult[1]);
    if ($n > 0) {
        $output = $strResult[1][0];
    } else {
        $output = $defaltthubmnail;
    }
    return $output;
}

function get_movie_image( $id ) {
    $data = get_movie_detail($id);
    if ($data) {
        $image = wpd_save_images($id,$data["data"][0]['poster']);
        return $image;
    }
    return false;
}

function display_movie_detail($id){
    $data = get_movie_detail($id);
    if (!$data) {
        return "<span>error, try later.</span>";
    }
    $output = '<div class="doulist-item"><div class="doulist-subject"><div class="post"><img src="'.  wpd_save_images($id,$data["data"][0]['poster']) .'"></div>';
    $output .= '<div class="content"><div class="title"><a href="//movie.douban.com/subject/'. $data["doubanId"] .'/" class="cute" target="_blank" rel="external nofollow">'. $data["data"][0]["name"] .'</a></div>';
    $output .= '<div class="rating"><span class="allstardark"><span class="allstarlight" style="width:' . $data["doubanRating"]*10 . '%"></span></span><span class="rating_nums"> ' . $data["doubanRating"]. ' </span><span>(' . $data["doubanVotes"]. '人评价)</span></div>';
    $output .= '<div class="abstract">导演 :';
    $directors = $data["director"];
    foreach ($directors as $director){
        $output .= $director["data"][0]["name"];
        if($key != count($directors) - 1){
            $output .= ' / ';
        }
    }
    $output .= '<br >演员: ';
    $casts = $data["actor"];
    foreach ($casts as $key=>$cast){
        $output .= $cast["data"][0]["name"];
        if($key != count($casts) - 1){
            $output .= ' / ';
        }
    }
    $output .= '<br >';
    $output .= '类型: ';
    $output .= $data["data"][0]["genre"];

    $output .= '<br >制片国家/地区: ';
    $output .= $data["data"][0]["country"];

    $output .= '<br>年份: ' . $data["year"] .'</div></div></div></div>';
    return $output;
}

/*将API替换为douban-imdb-api,
API文档:
https://movie.querydata.org/#/docs/sysl
 */
function get_movie_detail($id){
    $cache_key = WPD_CACHE_KEY . $type . '_' . $id;
    $cache =  get_transient($cache_key);
    if($cache) return $cache;
    $link = "https://movie.querydata.org/api?id=".$id;
    $img_link = "https://movie.querydata.org/api/generateimage?id=".$id;
    delete_transient($cache_key);

    //改用wordpress函数调用API 
    $response = @wp_remote_get($link);
    if (is_wp_error($response))
    {
        return false;
    }
    $content = json_decode(wp_remote_retrieve_body($response),true);
    set_transient($cache_key, $content, WPD_CACHE_TIME );

    return $content;
}

function wpd_save_images($id,$url){
    //改成png格式
    $e = ABSPATH .'douban_cache/'. $id .'.jpg';
    $t = WPD_CACHE_TIME;
    if ( !is_file($e) ) copy(htmlspecialchars_decode($url), $e);
    $url = home_url('/').'douban_cache/'. $id .'.jpg';
    return $url;
}

function wpd_load_scripts(){
    wp_enqueue_style('wpd-css', WPD_URL . "/assets/css/style.css", array(), WPD_VERSION, 'screen');
}

add_action('wp_enqueue_scripts', 'wpd_load_scripts');

require('embed.php');