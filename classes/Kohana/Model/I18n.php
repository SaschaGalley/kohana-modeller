<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Model_I18n extends ORM_Modeller_I18n {

    /**
     * Table name
     * @var string
     */
    protected $_table_name = 'I18n';

    /**
     * "Has many" connections
     * @var array
     */
    protected $_has_many = array(
        'translations'   => array('model' => 'I18n_Translation'),
    );

    protected $_translations = array();

    protected $_translations_changed = array();

    // -------------------------------------------------------------------------

    public function __toString()
    {
        $translation = $this->translations->where('language_id', '=', Modeller_I18n::language()->pk())->find();
        return (empty($translation->value)) ? '' : $translation->value;
    }

    // -------------------------------------------------------------------------

    /**
     * Checks if object data is set.
     *
     * @param  string $column Column name
     * @return boolean
     */
    public function __isset($column)
    {
        return (parent::__isset($column) OR in_array($column, Modeller_I18n::available_languages()));
    }

    // -------------------------------------------------------------------------

    public function get($key)
    {
        if (array_key_exists($key, $this->_translations))
        {
            return $this->_translations[$key];
        }

        if (isset($this->_object_name) AND in_array($key, Modeller_I18n::available_languages()))
        {
            $translation = $this->translations->where('language_id', '=', Modeller_I18n::language($key)->pk())->find();

            if ( ! $translation->loaded())
            {
                $translation = ORM::factory('I18n_Translation')->values(array(
                    'i18n_id'     => $this->id,
                    'language_id' => Modeller_I18n::language($key)->pk(),
                ));
            }

            $this->_translations[$key] = $translation;

            return $translation;
        }

        return parent::get($key);
    }

    // -------------------------------------------------------------------------

    public function set($key, $value)
    {
        if (in_array($key, Modeller_I18n::available_languages()))
        {
            $this->get($key)->value = $value;

            $this->_translations_changed[$key] = $this->get($key);

            return $this;
        }

        return parent::set($key, $value);
    }

    // -------------------------------------------------------------------------

    public function save(Validation $validation = NULL)
    {
        $result = parent::save($validation);

        foreach ($this->_translations_changed as $translation)
        {
            $translation->values(array('i18n_id' => $this->id))->save();
        }

        return $result;
    }

    // -------------------------------------------------------------------------

}
