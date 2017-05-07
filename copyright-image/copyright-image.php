<?php
/**
 *
 * Plugin Name: Image Copyrights
 * Plugin URI: http://onechapteraday.fr
 * Description: This plugin allows you to have the author of the featured image.
 * Version: 0.1
 * Author: Christelle Hilaricus
 * Author URI: http://onechapteraday.fr
 * License GPL2
 *
 */


# Enabling theme for featured image

add_theme_support ('post-thumbnails');


/**
 * Adding copyright fields to the media uploader $form_fields array
 *
 * @param array $form_fields
 * @param object $post
 *
 * @return array
 *
 */

function add_copyright_fields ($form_fields, $post) {
  $form_fields['copyright_field'] = array (
    'label' => __('Author'),
    'value' => get_post_meta($post->ID, '_custom_copyright', true),
  );

  $form_fields['copyright_link_field'] = array (
    'label' => __('URL'),
    'value' => get_post_meta($post->ID, '_custom_copyright_link', true),
    'helps' => 'Set the copyright of the picture'
  );

  return $form_fields;
}

add_filter ('attachment_fields_to_edit', 'add_copyright_fields', null, 2);


/**
 * Save the new copyright fields
 *
 * @param object $post
 * @param object $attachment
 *
 * @return array
 *
 */

function add_copyright_fields_save ($post, $attachment) {
  if (!empty($attachment['copyright_field']))
    update_post_meta($post['ID'], '_custom_copyright', esc_attr($attachment['copyright_field']));
  else
    delete_post_meta($post['ID'], '_custom_copyright');

  if (!empty($attachment['copyright_link_field']))
    update_post_meta($post['ID'], '_custom_copyright_link', esc_attr($attachment['copyright_link_field']));
  else
    delete_post_meta($post['ID'], '_custom_copyright_link');

  return $post;
}

add_filter('attachment_fields_to_save', 'add_copyright_fields_save', null, 2 );


/**
 * Display copyright author
 *
 * @param int $attachment_id
 *
 * @return array
 *
 */

function get_featured_image_copyright_author ($attachment_id = null) {
  $attachment_id = (empty($attachment_id )) ? get_post_thumbnail_id() : (int) $attachment_id;

  if ($attachment_id)
    return get_post_meta($attachment_id, '_custom_copyright', true);
}


/**
 * Display copyright link
 *
 * @param int $attachment_id
 *
 * @return array
 *
 */


function get_featured_image_copyright_link ($attachment_id = null) {
  $attachment_id = (empty($attachment_id )) ? get_post_thumbnail_id() : (int) $attachment_id;

  if ($attachment_id)
    return get_post_meta($attachment_id, '_custom_copyright_link', true);
}


/**
 * Display copyright with author and link
 *
 * @param int $attachment_id
 *
 * @return array
 *
 */

function get_featured_image_copyright ($attachment_id = null) {
  $attachment_id = (empty($attachment_id )) ? get_post_thumbnail_id() : (int) $attachment_id;

  if ($attachment_id) {
    $author = get_featured_image_copyright_author($attachment_id);
    $link = get_featured_image_copyright_link($attachment_id);

    if ($author == '' && $link == '')
      return;

    if ($author == '')
      return get_featured_image_copyright_link($attachment_id);

    if ($link == '')
      return get_featured_image_copyright_author($attachment_id);

    return '<a href="' . $link . '" target="_blank" rel="nofollow">&copy; ' . $author . '</a>';
  }
}


/**
 * Display copyright in featured image meta box
 *
 * @param string $content
 * @param string $post_ID
 * @param string $thumbnail_id
 *
 * @return array
 *
 */

function add_copyright_in_featured_image_metabox ($content, $post_ID, $thumbnail_id) {
  if ($thumbnail_id) {
    $mention = 'Copyright: ';
    $copy = ' <small>&copy; </small>';

    $author_id = '_custom_copyright';
    $link_id   = '_custom_copyright_link';

    $author_value = esc_attr(get_post_meta($thumbnail_id, $author_id, true));
    $link_value   = esc_attr(get_post_meta($thumbnail_id, $link_id, true));

    if (!$link_value && !$author_value)
      return $content;

    if (!$link_value)
      return $content .= $mention . $author_value . $copy . '<br />';

    if (!$author_value)
      return $content .= $mention . '<a href="' . $link_value . '" target="_blank" rel="nofollow">' . $link_value . '</a>' . $copy . '<br />';

    return $content .= $mention . '<a href="' . $link_value . '" target="_blank" rel="nofollow">' . $author_value . '</a>' . $copy . '<br />';
  }

  return $content;
}

add_filter ('admin_post_thumbnail_html', 'add_copyright_in_featured_image_metabox', null, 3);

?>
