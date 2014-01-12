<?php defined('SYSPATH') or die('No direct script access.');

/**
 * CRUD I18n Model
 *
 * @author Sascha Galley
 *
 */
class Kohana_ORM_Modeller_I18n extends ORM_Modeller {

    /**
     * @var array I18n columns
     */
    protected $_i18n_columns = array();

    /**
     * @var array I18n data
     */
    protected $_i18n_data = array();

    // -------------------------------------------------------------------------

    /**
     * Prepares the model database connection, determines the table name,
     * and loads column information.
     *
     * @return void
     */
    protected function _initialize()
    {
        foreach ($this->_i18n_columns as $i18n)
        {
            $this->_has_one[$i18n] = array('model' => 'I18n', 'foreign_key' => $i18n);
        }

        parent::_initialize();
    }

    // -------------------------------------------------------------------------

    /**
     * Search functionality
     *
     * @param  String search for
     * @return ORM_Modeller this
     */
    public function search($query = NULL)
    {
        if ( ! empty($query) AND sizeof($this->searchable_columns()) > 0)
        {
            $this->where_open();

            foreach ($this->searchable_columns() as $column)
            {
                if (in_array($column, $this->i18n_columns()))
                {
                    $this->or_where(DB::expr('`'.$this->object_name().'`.`'.$column.'`'), 'IN', DB::expr('(SELECT i18n_id FROM I18n_Translations WHERE i18n_id=`'.$this->object_name().'`.`'.$column.'` AND value like "%'.$query.'%")'));
                }
                else
                {
                    $this->or_where($column, 'LIKE', '%'.$query.'%');
                }
            }

            $this->where_close();
        }

        return $this;
    }

    // -------------------------------------------------------------------------

    /**
     *
     */
    public function split_i18n_column($column)
    {
        if (($seperator = strrpos($column, '_')) !== FALSE)
        {
            $check_column   = substr($column, 0, strrpos($column, '_'));
            $check_language = substr($column, strrpos($column, '_') + 1);

            if (in_array($check_language, Modeller_I18n::available_languages()) AND in_array($check_column, $this->i18n_columns()))
            {
                return array('column' => $check_column, 'language' => $check_language);
            }
        }

        return FALSE;
    }

    // -------------------------------------------------------------------------

    /**
     * Handles getting of column
     * Override this method to add custom get behavior
     *
     * @param   string $column Column name
     * @throws Kohana_Exception
     * @return mixed
     */
    public function get($column)
    {
        if (parent::__isset($column) AND ! in_array($column, $this->i18n_columns()))
        {
            // This helps us prevent nesting issues
            return parent::get($column);
        }

        if (($translation = $this->split_i18n_column($column)) !== FALSE)
        {
            $i18n = parent::get($translation['column']);
            $i18n = empty($i18n) ? ORM::factory('I18n') : $i18n;
            $i18n = ($i18n instanceof Model_I18n) ? $i18n : ORM::factory('I18n', $i18n);

            return $i18n->$translation['language'];
        }

        if (in_array($column, $this->i18n_columns()))
        {
            $i18n = parent::get($column);
            $i18n = empty($i18n) ? ORM::factory('I18n') : $i18n;
            $i18n = ($i18n instanceof Model_I18n) ? $i18n : ORM::factory('I18n', $i18n);

            return $i18n;
        }

        return parent::get($column);
    }

    // -------------------------------------------------------------------------

    /**
     * Handles setting of columns
     * Override this method to add custom set behavior
     *
     * @param  string $column Column name
     * @param  mixed  $value  Column value
     * @throws Kohana_Exception
     * @return ORM
     */
    public function set($column, $value)
    {
        if (parent::__isset($column) AND ! in_array($column, $this->i18n_columns()))
        {
            // This helps us prevent nesting issues
            return parent::set($column, $value);
        }

        if (($translation = $this->split_i18n_column($column)) !== FALSE)
        {
            $i18n = parent::get($translation['column']);
            $i18n = empty($i18n) ? ORM::factory('I18n') : $i18n;
            $i18n = ($i18n instanceof Model_I18n) ? $i18n : ORM::factory('I18n', $i18n);

            parent::set($translation['column'], $i18n);

            $i18n->$translation['language'] = $value;

            return $this;
        }

        return parent::set($column, $value);
    }

    // -------------------------------------------------------------------------

    /**
     * Set values from an array with support for one-one relationships.  This method should be used
     * for loading in post data, etc.
     *
     * @param  array $values   Array of column => val
     * @param  array $expected Array of keys to take from $values
     * @return ORM
     */
    public function values(array $values, array $expected = NULL)
    {
        // Default to expecting everything except the primary key
        if ($expected === NULL)
        {
            $expected = array_keys($this->_table_columns);

            foreach ($this->i18n_columns() as $i18n)
            {
                foreach (Modeller_I18n::available_languages() as $lang)
                {
                    $expected[] = $i18n.'_'.$lang;
                }
            }

            // Don't set the primary key by default
            unset($values[$this->_primary_key]);
        }

        return parent::values($values, $expected);
    }

    // -------------------------------------------------------------------------

    /**
     * Updates or Creates the record depending on loaded()
     *
     * @chainable
     * @param  Validation $validation Validation object
     * @return ORM
     */
    public function save(Validation $validation = NULL)
    {
        $i18n_columns = array();

        foreach ($this->i18n_columns() as $i18n)
        {
            if ($this->$i18n instanceof Model_I18n)
            {
                // Save the I18n
                $this->$i18n->save();

                // Remember column
                $i18n_columns[$i18n] = $this->$i18n;

                // Since the I18n model won't return its primary key
                // in __toString we need to do it manually here
                $this->$i18n = $this->$i18n->pk();
            }
        }

        $result = parent::save($validation);

        foreach ($i18n_columns as $key => $i18n)
        {
            $this->$key = $i18n;
        }

        return $result;
    }

    // -------------------------------------------------------------------------

    /**
     * Searchable columns
     *
     * @return array
     */
    public function i18n_columns($full_info = FALSE)
    {
        return $this->_get_info($this->_i18n_columns, $this->list_columns(), $full_info, FALSE);
    }

    // -------------------------------------------------------------------------

    /**
     * Editable columns
     *
     * @return array
     */
    public function editable_columns($full_info = FALSE)
    {
        // Get all editable columns
        $editable_columns = parent::editable_columns();

        // Get editable i18n columns
        $editablie_i18n = array_intersect(parent::editable_columns(), $this->i18n_columns());

        foreach ($editablie_i18n as $i18n)
        {
            // Create array of column_lang strings from available languages
            $additional = array_map(function($lang) use ($i18n){
                return $i18n.'_'.$lang;
            }, Modeller_I18n::available_languages());

            // Replace editable column with array of possible languages
            $editable_columns[array_search($i18n, $editable_columns)] = $additional;
        }

        // Return flattened array
        return $this->_get_info(Arr::flatten($editable_columns), $this->list_columns(), $full_info, FALSE);
    }

    // -------------------------------------------------------------------------

}