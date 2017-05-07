# WordPress plugins

## Image Copyrights

This plugin gives you the possibility to add a copyright for each image uploaded with the Media Uploader of WordPress. It also allows you to display this copyright in your templates. New functions exist for you to get the copyright author or/and link of your featured image.

You can actually see the copyright, once you add it, in the metabox of your featured image.

## Custom Taxonomy Location

This plugin create a location taxonomy in your WordPress administration. It automatically adds all continents and countries of the world in your new location taxonomy with the proper hierarchy (continent > country).

## Custom Taxonomy Person

This plugin creates a complete administration for persons. I use it mostly for artists on music blog posts, but you can actually use it for any kind of persons/celebrities. With *Person Custom Taxonomy*, you can inform your readers about the basic information of every person term : birthdate, deathdate, real name, social links...

You can also add persons on your uploaded pictures thanks to this plugin. It also displays on your dashboard a small widget with the birthdays of the month among your persons created.

## Post Dictionary

The main purpose of this plugin is to give you the possibility to add a dictionary on all your WordPress posts. It's like having a glossary on one specific post. You can add, edit and delete every entries of this glossary. You can also style your data by using the classes provided in the plugin.

This plugin uses the [`dl`](https://developer.mozilla.org/en/docs/Web/HTML/Element/dl) [`dt`](https://developer.mozilla.org/en/docs/Web/HTML/Element/dt) [`dd`](https://developer.mozilla.org/en/docs/Web/HTML/Element/dd) tags for the definitions.

## Random Post

This plugin provides you a url where a post is displayed randomly. You can, for example, add a new item in your menu with the random url setted. The default url is `/random-post`.

## Remove Emojis

This plugin helps you to remove the emojis provided since the version 4.2 of WordPress. It removes those code blocks from your header.

```
<script type="text/javascript">
  window._wpemojiSettings = {"baseUrl":"https:\/\/s.w.org\/images\/core\/emoji\/2.2.1\/72x72\/","ext":".png","svgUrl":"https:\/\/s.w.org\/images\/core\/emoji\/2.2.1\/svg\/","svgExt":".svg","source":{"concatemoji":"http:\/\/wordpress:8888\/wp-includes\/js\/wp-emoji-release.min.js?ver=4.7.3"}};
  !function(a,b,c){function d(a){var b,c,d,e,f=String.fromCharCode;if(!k||!k.fillText)return!1;switch(k.clearRect(0,0,j.width,j.height),k.textBaseline="top",k.font="600 32px Arial",a){case"flag":return k.fillText(f(55356,56826,55356,56819),0,0),!(j.toDataURL().length<3e3)&&(k.clearRect(0,0,j.width,j.height),k.fillText(f(55356,57331,65039,8205,55356,57096),0,0),b=j.toDataURL(),k.clearRect(0,0,j.width,j.height),k.fillText(f(55356,57331,55356,57096),0,0),c=j.toDataURL(),b!==c);case"emoji4":return k.fillText(f(55357,56425,55356,57341,8205,55357,56507),0,0),d=j.toDataURL(),k.clearRect(0,0,j.width,j.height),k.fillText(f(55357,56425,55356,57341,55357,56507),0,0),e=j.toDataURL(),d!==e}return!1}function e(a){var c=b.createElement("script");c.src=a,c.defer=c.type="text/javascript",b.getElementsByTagName("head")[0].appendChild(c)}var f,g,h,i,j=b.createElement("canvas"),k=j.getContext&&j.getContext("2d");for(i=Array("flag","emoji4"),c.supports={everything:!0,everythingExceptFlag:!0},h=0;h<i.length;h++)c.supports[i[h]]=d(i[h]),c.supports.everything=c.supports.everything&&c.supports[i[h]],"flag"!==i[h]&&(c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&c.supports[i[h]]);c.supports.everythingExceptFlag=c.supports.everythingExceptFlag&&!c.supports.flag,c.DOMReady=!1,c.readyCallback=function(){c.DOMReady=!0},c.supports.everything||(g=function(){c.readyCallback()},b.addEventListener?(b.addEventListener("DOMContentLoaded",g,!1),a.addEventListener("load",g,!1)):(a.attachEvent("onload",g),b.attachEvent("onreadystatechange",function(){"complete"===b.readyState&&c.readyCallback()})),f=c.source||{},f.concatemoji?e(f.concatemoji):f.wpemoji&&f.twemoji&&(e(f.twemoji),e(f.wpemoji)))}(window,document,window._wpemojiSettings);
</script>
```

```
<style>
  img.wp-smiley,
  img.emoji {
    display: inline !important;
    border: none !important;
    box-shadow: none !important;
    height: 1em !important;
    width: 1em !important;
    margin: 0 .07em !important;
    vertical-align: -0.1em !important;
    background: none !important;
    padding: 0 !important;
  }
</style>
```

## Remove jQuery Migrate

This helps you to remove jQuery migrate which is only necessary if a website have deprecated jQuery functions. It removes from your header the call of the jQuery Migrate script located in wp-includes.

```
.
+-- wp-admin
+-- wp-content
+-- wp-includes
|   +-- js
|       +-- jquery
|           +-- jquery-migrate.min.js
|   +-- ...
+-- ...
```

## Remove WordPress Admin Bar

This plugin helps you to get rid of the WordPress admin bar (files and inline styles) provided since the version 3.1 of WordPress. There won't be anymore calls for `dashicons.min.css` and `admin-bar.min.css` in your front template.

```
.
+-- wp-admin
+-- wp-content
+-- wp-includes
|   +-- css
|       +-- dashicons.min.css
|       +-- admin-bar.min.css
|   +-- ...
+-- ...
```

This code block will also be removed.

```
<style type="text/css" media="screen">
  html { margin-top: 32px !important; }
  * html body { margin-top: 32px !important; }
  @media screen and ( max-width: 782px ) {
    html { margin-top: 46px !important; }
    * html body { margin-top: 46px !important; }
  }
</style>
```
