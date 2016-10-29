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
    'helps' => 'Set a copyright credit for the attachment'
  );

  $form_fields['copyright_link_field'] = array (
    'label' => __('Author URL'),
    'value' => get_post_meta($post->ID, '_custom_copyright_link', true),
    'helps' => 'Set a copyright link for the attachment'
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
    update_post_meta($post['ID'], '_custom_copyright', $attachment['copyright_field'] );
  else
    delete_post_meta($post['ID'], '_custom_copyright' );

  if (!empty($attachment['copyright_link_field']))
    update_post_meta($post['ID'], '_custom_copyright_link', $attachment['copyright_link_field'] );
  else
    delete_post_meta($post['ID'], '_custom_copyright_link' );

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
 *
 * @return array
 *
 */

function add_copyright_in_featured_image_metabox ($content) {
  $copyright = get_featured_image_copyright();

  if ($copyright)
    return $content = 'Image copyright : ' . get_featured_image_copyright() . $content;

  return $content;
}

add_filter ('admin_post_thumbnail_html', 'add_copyright_in_featured_image_metabox', null, 2);

?>
