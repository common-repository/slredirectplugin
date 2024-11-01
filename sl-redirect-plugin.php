<?php
/*
Plugin Name: SLRedirectPlugIn
Plugin URI: http://www.steffen-liersch.de/wordpress/
Description: This plug-in can generate redirections to relocated pages or posts. Furthermore an alternative implementation of wp_redirect can be installed for the case that the correct HTTP status code is not sent.
Version: 1.0
Author: Steffen Liersch
Author URI: http://www.steffen-liersch.de/
*/

/*----------------------------------------------------------------
 * Setup
 *----------------------------------------------------------------*/

if(function_exists('add_action'))
  add_action('admin_menu', 'sl_redirect_admin_menu');

// Arguments: tag, function, priority (default is 10), accepted_args (default is 1)
if(function_exists('add_filter'))
  add_filter('plugin_action_links', 'sl_redirect_plugin_action_links', 10, 2);

function sl_redirect_plugin_action_links($links, $file)
{
  static $plugin;
  if(!$plugin)
    $plugin=plugin_basename(__FILE__);
  if($file==$plugin )
  {
    $settings='<a href="options-general.php?page=sl_redirect_settings">'.__('Settings').'</a>';
    array_unshift($links, $settings); // At first
  }
  return $links;
}

function sl_redirect_admin_menu()
{
  // Arguments: page_title, menu_title, capability, handle/file, function
  add_options_page(
    'SLRedirectPlugIn Settings',
    'SL::Redirect',
    'administrator',
    'sl_redirect_settings',
    'sl_redirect_options_page');
}

function sl_redirect_options_page()
{
  if(!current_user_can('manage_options')) wp_die();

  if($_POST['action']=='update')
  {
    $option='sl_redirect_404';
    $value=trim($_POST[$option]);
    $value=stripslashes($value);
    $value=$value=='yes' ? 'yes' : 'no';
    update_option($option, $value);

    $option='sl_redirect_fix';
    $value=trim($_POST[$option]);
    $value=stripslashes($value);
    $value=$value=='yes' ? 'yes' : 'no';
    update_option($option, $value);

    echo '<div id="message" class="updated fade"><p><strong>'.__('Options saved.', 'Steffen Liersch').'</strong></p></div>';
  }
?>
  <div class="wrap">
    <h2><?php echo _e('SLRedirectPlugIn Settings', 'Steffen Liersch'); ?></h2>
    <form method="post" action="options-general.php?page=sl_redirect_settings">
      <?php wp_nonce_field('sl_redirect_settings'); ?>
      <p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Changes', 'Steffen Liersch'); ?>" /></p>
      <table class="form-table">
        <tr valign="top">
          <th scope="row"><?php _e('Redirection to relocated pages or posts', 'Steffen Liersch'); ?></th>
          <td>
            <fieldset>
              <?php
              $option='sl_redirect_404';
              echo '<label for="'.$option.'">';
              $value=get_option($option, 'yes');
              $checked=$value=='yes' ? ' checked="checked"' : '';
              echo '<input id="'.$option.'" name="'.$option.'" type="checkbox" value="yes"'.$checked.' /> ';
              echo __('Redirect to relocated pages or posts with the same name', 'Steffen Liersch').'</label>';
              echo '<br />';
              echo '<span class="description">';
              echo __('Not found pages or posts are searched by name only. If a page or post was found, the request will be permanently redirected to the new location (HTTP status code 301). This option helps, for instance, if the category URL has changed.', 'Steffen Liersch');
              echo '</span>';
              ?>
            </fieldset>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><?php _e('Alternative implementation of function wp_redirect', 'Steffen Liersch'); ?></th>
          <td>
            <fieldset>
              <?php
              $option='sl_redirect_fix';
              echo '<label for="'.$option.'">';
              $value=get_option($option, 'no');
              $checked=$value=='yes' ? ' checked="checked"' : '';
              echo '<input id="'.$option.'" name="'.$option.'" type="checkbox" value="yes"'.$checked.' /> ';
              echo __('Enable another wp_redirect function to return the correct HTTP status code', 'Steffen Liersch').'</label>';
              echo '<br />';
              echo '<span class="description">';
              echo __('This option installs another implementation of function wp_redirect that does not care about FastCGI. As a result the correct HTTP status code is returned. Permanently redirections will be enabled (HTTP status code 301). Please be careful with this option. Use it at your own risk.', 'Steffen Liersch');
              echo '<br/><br/>Current interface between web server and PHP: '.php_sapi_name();
              if(php_sapi_name()!='cgi-fcgi')
                echo '<br/>Option is not recommended for your web server.';
              else echo ' (FastCGI)<br/>Try this function at your own risk if you have problems with the returned HTTP status code.';
              echo '</span>';
              ?>
            </fieldset>
          </td>
        </tr>
      </table>
      <p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Changes', 'Steffen Liersch'); ?>" /></p>
      <input type="hidden" name="action" value="update" />
    </form>
    <p>If you like this plug-in, you can leave a donation to support maintenance and development.</p>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post"> <input name="cmd" type="hidden" value="_s-xclick" /><input name="hosted_button_id" type="hidden" value="ZD44XQ6YP9KYU" /><input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" type="image" /><img src="https://www.paypal.com/de_DE/i/scr/pixel.gif" border="0" alt="" width="1" height="1" /></form>
    <h3>Copyright</h3>
    <p>
     Copyright &copy; 2010 Dipl.-Ing. (BA) Steffen Liersch<br />
     <a href="http://www.steffen-liersch.de/">www.steffen-liersch.de</a>
    </p>
  </div>
<?php
}

/*----------------------------------------------------------------
 * Redirect to relocated pages or posts with the same name
 *----------------------------------------------------------------*/

require_once('sl-redirect.php');

function sl_template_redirect()
{
  global $wp_query;
  if($wp_query)
  {
    // Requested document not found?
    if($wp_query->is_404)
    {
      // Try to find a published post with the base name
      global $wpdb;
      $name=basename($_SERVER['REQUEST_URI']);
      $name=addslashes($name);
      $id=$wpdb->get_var('SELECT ID FROM '.$wpdb->posts.' WHERE post_name="'.$name.'" AND post_status="publish"');

      // Redirect permanently to the found post
      if($id)
      {
        $location=get_permalink($id);
        sl_redirect_exit($location, true); // Moved permanently
      }
    }
  }
}

if(function_exists('add_action'))
{
  $value=get_option('sl_redirect_404', 'yes');
  if($value=='yes')
    add_action('template_redirect', 'sl_template_redirect');
}

/*----------------------------------------------------------------
 * Another wp_redirect function to correct the HTTP status code
 *----------------------------------------------------------------*/

$value=get_option('sl_redirect_fix', 'no');
if($value=='yes') :

/*
The following code defines a modified implementation of
function wp_redirect. The original WordPress function
is located in file "wp-includes/pluggable.php".
*/
if(!function_exists('wp_redirect')) :
function wp_redirect($location, $status=302)
{
  global $is_IIS;

  $location=apply_filters('wp_redirect', $location, $status);
  $status=apply_filters('wp_redirect_status', $status, $location);

  if(!$location) // Allows the wp_redirect filter to cancel a redirect
    return false;

  $location=wp_sanitize_redirect($location);

  if($is_IIS)
  {
    //header("Refresh: 0;url=$location");
    header("Location: $location");
  }
  else
  {
    //if(php_sapi_name()!='cgi-fcgi')
    //  status_header($status); // This causes problems on IIS and some FastCGI setups
    status_header($status); // This causes problems on IIS and some FastCGI setups
    header("Location: $location");
  }
}
endif;

/*
The following function is required for wp_redirect. The original
WordPress function is located in file "wp-includes/pluggable.php".
*/
if(!function_exists('wp_sanitize_redirect')) :
function wp_sanitize_redirect($location)
{
  $location=preg_replace('|[^a-z0-9-~+_.?#=&;,/:%!]|i', '', $location);
  $location=wp_kses_no_null($location);

  // Remove %0d and %0a from location
  $strip=array('%0d', '%0a', '%0D', '%0A');
  $location=_deep_replace($strip, $location);
  return $location;
}
endif;

endif; // if($value=='yes')

?>