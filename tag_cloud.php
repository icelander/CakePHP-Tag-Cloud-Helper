<?php

/**
* Tag Cloud Helper
*/
class TagCloudHelper extends AppHelper
{
   var $helpers = array('Html');
   
   var $options = array(
         'max_font_size' => 32,
         'min_font_size' => 12,
         'font_size_unit' => 'px',

         'name_field' => 'name',
         'id_field' => 'id',
         'model_name' => 'Tag',
         'count_field' => 'count',
         
         'url' => array('controller' => 'tags', 'action' => 'index', 'id' => null),
         'htmlAttributes' => array(),
         'confirmMessage' => false,
         'escapeTitle' => true,
         
         'separator' => ' ',
         'link_title_format' => ':count things tagged with :tag_name',
      );
   
   function generate($tags, $options = array())
   {
      $output_array = array();
      
      if (count($tags) == 0)
      {
         return;
      }
      
      $this->options = array_merge($this->options, $options);
      $o = $this->options;
		 
      $max_size = $o['max_font_size']; // max font size in pixels
      $min_size = $o['min_font_size']; // min font size in pixels
    
      foreach ($tags as $tag)
      {
         if (isset($tag[$o['model_name']]))
         {
            $count = $tag[$o['model_name']][$o['count_field']];
         }
         else
         {
            $count = $tag[$o['count_field']];
         }
         
         if (!isset($max_count))
         {
            $max_count = $count;
         }
         elseif ($count > $max_count)
         {
            $max_count = $count;
         }
         
         if (!isset($min_count))
         {
            $min_count = $count;
         }
         elseif ($count < $min_count)
         {
            $min_count = $count;
         }
      }
      
      $spread = (($max_count - $min_count) > 0)?($max_count - $min_count):1;
    
      // set the font-size increment
      $step = ($o['max_font_size'] - $o['min_font_size']) / ($spread);
    
      foreach ($tags as $tag) 
      {
         // calculate font-size
         // find the $value in excess of $min_qty
         // multiply by the font-size increment ($size)
         // and add the $min_size set above
         if (isset($tag[$o['model_name']]))
         {
            $count = $tag[$o['model_name']][$o['count_field']];
            $tag_name = $tag[$o['model_name']][$o['name_field']];
            $tag_id = $tag[$o['model_name']][$o['id_field']];
         }
         else
         {
            $count = $tag[$o['count_field']];
            $tag_name = $tag[$o['name_field']];
            $tag_id = $tag[$o['id_field']];
         }
         
         $font_size = round($o['min_font_size'] + (($count - $min_count) * $step));
         $link_name = $tag_name;
         $link_url = $o['url'];
         $link_url['id'] = $tag_id;
         $font_size = $font_size . $o['font_size_unit'];
         $htmlAttributes = array();
         
         if (isset($o['link_title_format']))
         {
            $link_title = $o['link_title_format'];
            
            $link_title = str_replace(':count', $count, $link_title);
            $link_title = str_replace(':tag_name', $tag_name, $link_title);
            
            $htmlAttributes['title'] = $link_title;
         }
         
         if (isset($o['htmlAttributes']['style']))
         {
            $htmlAttributes['style'] = $this->replace_font_size($o['htmlAttributes']['style'], $font_size);
         }
         else
         {
            $htmlAttributes['style'] = 'font-size:' . $font_size . ';';
         }
         
         $output_array[] = $this->Html->link($link_name, $link_url, $htmlAttributes, $o['confirmMessage'], $o['escapeTitle']);
      }
      
      return $this->output(implode($o['separator'], $output_array));
   }
   
   private function replace_font_size($style, $font_size)
   {
      $style = explode(';', $style);

      foreach ($style as $key => $val)
      {
         if (stristr($val, 'font-size:') || strlen($val) == 0)
         {
            continue;
         }

         $new_style[] = $val;
      }

      $new_style[] = 'font-size: ' . $font_size;

      return implode(';',$new_style);
   }
}


?>