# wp-douban-movie

可以以豆列的方式展示电影。

图片内容缓存到本地解决豆瓣防盗链。

读取API数据缓存时间为1个月。

因为豆瓣API已经没有公开的API KEY，所以将API换成了[douban-imdb-api](https://www.iqi360.com/p/douban-imdb-api)。目前只有电影接口。

## 使用方法
### 调用电影封面

使用函数`get_movie_image($id)`,id为豆瓣电影数字id

使用插件内置的缩略图函数`wpd_get_post_image($id)`,id为文章id


### 插入方式

1. 直接在文章中粘贴豆瓣url 即可。(wp_embed必须开启)

<pre data-type="shortcode">https://movie.douban.com/subject/24751763</pre>

2. 或者使用短代码

<pre>[douban id="26862829"]</pre>

## 注意
因为API作者表示不能在15秒内连续使用该API，所以建议缓存一部电影之后再追加一条，不要一起追加。如果出现无法获取电影信息的提示，请稍后重试（等前面的一部缓存上了就好了）。


