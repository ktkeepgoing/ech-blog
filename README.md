# ech-blog
A Wordpress plugin to display ECH articles for any ECH company's brand websites. It is integrated with the global ECH articles CMS. 

## Installation
You need to manually create some pages and paste some files in order to make this plugin works. 
Follow the below steps. 

1. Install and activate the ech-blog plugin.
2. Create two pages: 
    - Health Blog Content with slug `health-blog-content`. :point_left: for display the single post content
    - Health Blog Category Tag List with slug `health-blog-category-tag-list`. :point_left: for display the filtered articles list. 
3. Copy and paste 2 php files from the setup folder into the `astra-child` folder.
    - `setup/pages/page-health-blog-content.php`
    - `setup/pages/page-health-blog-category-tag-list.php`
4. Copy and paste 3 css files from the setup folder into the `astra-child/assets/css` folder.
    - `setup/css/ech-blog-cate-tags-list.css`
    - `setup/css/ech-blog-single-post.css`
    - `ech-blog-single-post-zh.css`

:information_desk_person: You may need to edit the css files to match the brand style and design requirements if necessary.


## Usage 
To display the blog, enter shortcode
```
[ech_blog]
```

### Shortcode Attributes
- ppp: post per page. Default vaule is `12`
- channel_id: select article channels between ECH app and website. Default value is `9` (website)
- brand_id: enter brand id to display specific brand articles. Default value is `0` which is display all brand articles
- dev_env: Default is `false`. Edit to `true` for local XAMPP website. 

