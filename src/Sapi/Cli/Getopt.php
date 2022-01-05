<?php
namespace Otto\Sapi\Cli;

class Getopt
{
    /**
     *
     * The values represented by this object.
     *
     * @var array
     *
     */
    protected $values;

    /**
     *
     * Any getopt parsing errors.
     *
     * @var array
     *
     */
    protected $errors = array();

    /**
     *
     * Constructor.
     *
     * @param array $values The values to be represented by this object.
     *
     * @param array $errors Any getopt parsing errors.
     *
     */
    public function __construct(
        array $values = array(),
        array $errors = array()
    ) {
        $this->values = $values;
        $this->errors = $errors;
    }

    /**
     *
     * Returns a value by key, an alternative value if that key does not exist,
     * or all values if no key is passed.
     *
     * @param string $key The key, if any, to get the value of; if null, will
     * return all values.
     *
     * @param string $alt The alternative default value to return if the
     * requested key does not exist.
     *
     * @return mixed The requested value, or the alternative default
     * value; or, if no key was passed, all values.
     *
     */
    public function get($key = null, $alt = null)
    {
        if ($key === null) {
            return $this->values;
        }

        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
        }

        return $alt;
    }

    /**
     *
     * Are there error messages related to getopt parsing?
     *
     * @return bool
     *
     */
    public function hasErrors()
    {
        return $this->errors ? true : false;
    }

    /**
     *
     * Returns the error messages related to getopt parsing.
     *
     * @return array
     *
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
