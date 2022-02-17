<?php
/**
 * This class is used throughout the plugin for
 * tools that are useful.
 */

class Virtue_Survey_Tools{
  public static $virtue_list = array();
    function __construct(){
      $this-> $virtue_list = array(
          'prudence',
          'justice',
          'fortitude',
          'temperance',
          'affability',
          'courtesy',
          'gratitude',
          'kindness',
          'loyalty',
          'obedience',
          'patriotism',
          'prayerfulness',
          'religion',
          'respect',
          'responsibility',
          'sincerity',
          'trustworhiness',
          'circumspection',
          'docility',
          'foresight',
          'industriousness',
          'magnanimity',
          'magnificence',
          'patience',
          'perseverance',
          'honesty',
          'humiliy',
          'meekness',
          'moderation',
          'modesty',
          'orderliness',
          'self-control'
      );
    }

    public function get_virtue_list(){
      return $this->virtue_list;
    }


}
