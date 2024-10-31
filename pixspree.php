<?php
/*
 
  Plugin Name: PixSpree
  Plugin URI: http://pixspree.com
  Description: This plugin enables the PixSpree service on your blog.
  Version: 0.1
  Author: PixSpree
  Author URI: http://pixspree.com
  License: GPL2
 
    Copyright YEAR  Kyle Campbell  (email : kyle@pixspree.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!class_exists('PixSpree'))
{  
  class PixSpree{
    
    public function init()
    {
      if(!is_admin()) self::api();
           
      if(get_option('ps_requires_setup') == '1')
      {
        update_option('ps_requires_setup','0');
        self::api('setup','setup/?activate=true&host='.$_SERVER['HTTP_HOST']);
      }
    }

    public function activate()
    {
      add_option('ps_requires_setup','1');
    }
    
    public function deactivate()
    {
      delete_option('ps_requires_setup');
    }
     
    public function clean_script($src)
    {
      if ( strpos($src, 'widget.pixspree.com') > -1 )
      {
        if( strpos($src, 'widget.pixspree.com/setup') > -1 )
        {
          $new_src = explode('&ver', $src);  
          return $new_src[0];
        }
        else
        {             
          $new_src = explode('?', $src);  
          return $new_src[0];
        }        
      }      
    }

    private function api($name='widget',$uri=false)
    {
      $uri = ($uri)?$uri:$_SERVER['HTTP_HOST'];
      wp_deregister_script('ps_'.$name);
      wp_register_script('ps_'.$name, 'http://widget.pixspree.com/'.$uri,false);
      wp_enqueue_script('ps_'.$name);      
    }
           
  }
    
  register_activation_hook( __FILE__, array('PixSpree', 'activate'));
  register_deactivation_hook( __FILE__, array('PixSpree', 'deactivate'));
  add_filter('script_loader_src', array('PixSpree', 'clean_script'));
  add_action('init', array('PixSpree', 'init'));  
}
?>
