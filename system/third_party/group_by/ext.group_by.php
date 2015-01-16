<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package     ExpressionEngine
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license     http://expressionengine.com/user_guide/license.html
 * @link        http://expressionengine.com
 * @since       Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Group By Extension
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    Extension
 * @author      Nathan Pitman
 * @link        http://www.ninefour.co.uk
 */

class Group_by_ext {

    public $settings        = array();
    public $description     = 'Adds Group By support to Channel Entries loops';
    public $docs_url        = 'http://www.ninefour.co.uk/labs/';
    public $name            = 'Group By';
    public $settings_exist  = 'n';
    public $version         = '1.0.2';

    /**
     * Constructor
     *
     * @param   mixed   Settings array or empty string if none exist.
     */
    public function __construct($settings = '')
    {
        $this->settings = $settings;
    }// ----------------------------------------------------------------------

    /**
     * Activate Extension
     *
     * This function enters the extension into the exp_extensions table
     *
     * @see http://codeigniter.com/user_guide/database/index.html for
     * more information on the db class.
     *
     * @return void
     */
    public function activate_extension()
    {
        // Setup custom settings in this array.
        $this->settings = array();

        $data = array(
            'class'     => __CLASS__,
            'method'    => 'group_by',
            'hook'      => 'channel_entries_query_result',
            'settings'  => serialize($this->settings),
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        ee()->db->insert('extensions', $data);

        // No hooks selected, add in your own hooks installation code here.
    }

    // ----------------------------------------------------------------------

    /**
     * group_by
     *
     * @param
     * @return
     */
    public function group_by($obj, $query_result)
    {

        // Get the latest version of $query_result
        if (ee()->extensions->last_call !== FALSE) {
            $query_result = ee()->extensions->last_call;
        }

        // Check for our parameter from the exp:channel:entries tag
        $group_by = ee()->TMPL->fetch_param('groupby');

        // Return early if parameter is not found. Nothing else to do.
        if (is_null($group_by) OR empty($group_by)) {
            return $query_result;
        }

        $existing = array();
        $grouped_result = array();

        foreach ($query_result as $entry) {
            if (isset($entry[$group_by]) AND !empty($entry[$group_by])) {
                $needle = (string)$entry[$group_by];
                if (array_search($needle, $existing)===FALSE) {
                    array_push($existing, $needle);
                    array_push($grouped_result, $entry);
                }
            }
        }

        if (!empty($grouped_result)) {
            return $grouped_result;
        }

        return $query_result;
    }

    // ----------------------------------------------------------------------

    /**
     * Disable Extension
     *
     * This method removes information from the exp_extensions table
     *
     * @return void
     */
    function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');
    }

    // ----------------------------------------------------------------------

    /**
     * Update Extension
     *
     * This function performs any necessary db updates when the extension
     * page is visited
     *
     * @return  mixed   void on update / false if none
     */
    function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }
    }

    // ----------------------------------------------------------------------
}

/* End of file ext.group_by.php */
/* Location: /system/expressionengine/third_party/group_by/ext.group_by.php */
