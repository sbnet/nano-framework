<?php
/**
* Form manipulation
*
* @package NanoFramework\Utilities
*/
namespace NanoFramework\Utilities;

/**
* Class for form, handle values and validation
*
* But not html form generation, maybe it will never be done here
* 
* @package NanoFramework\Utilities
* @class Form
* @author Stéphane BRUN
*/
class Form
{ 
    private $values = array();   
    private $errors = array();
    
    /**
    * Magic Getter
    *
    * Example of use : 
    * <code>
    *    $tmp = $my_form->name;
    *    // is the same as :
    *    $tmp = $my_form->get_value('name');
    * </code>
    *
    * @param string name of the variable to get
    * @return mixed value of the variable
    */    
    public function __get($name)
    {
        return $this->get_value($name);
    }
    
    /**
    * Store a value
    *
    * @param string name of the variable to store
    * @param mixed value of the variable
    * @param string name of the validator function (callback)
    * @param array parameters for the callback    
    */    
    public function set($name, $value, $validator=null, $params=array())
    {
        $this->values[$name]['value'] = $value;
        if($validator)
        {
            $this->add_validator($name, $validator, $params);
        }
    }
    
    /**
    * Get a stored value
    *
    * @param string name of the variable to get
    * @return mixed value of the variable
    */
    public function get_value($name)
    {     
        if(array_key_exists($name, $this->values))
        {
            return $this->values[$name]['value'];
        }
    }
    
    /**
    * Get all the stored values
    *
    * @return array values of the form
    */    
    public function get_values()
    {
        $values = array();
        
        foreach($this->values as $name=>$value)
        {
            $values[$name] = $this->get_value($name);
        }
        
        return $values;
    }

    /**
    * Add a validator to a stored var
    *
    * The callback function must return null in case of success or
    * a string (the error message) if the variable doesn't validate.
    *
    * $params array can get any values you want to pass to the callback
    * but <i>message</i> is reserved for the error message returned if
    * the validation doesn't apply.   
    *
    * @param string name of the variable 
    * @param string name of the validator function (callback)
    * @param array parameters for the callback
    */
    public function add_validator($name, $validator, $params=array())
    {
        $this->values[$name]['validators'][] = array(
            'function'=>$validator, 
            'params'=>$params
        );
    }
    
    /**
    * Check for errors
    * 
    * @param string name of the variable to validate
    * @return bool true if the variable is valid
    */
    public function is_valid($name=null)
    {              
        // Validate a single variable
        if($name!==null)
        {
            if(array_key_exists($name, $this->values))
            {
                return $this->validate($name, $this->values[$name]);                
            }
            else
            {
                return false;
            }
        }
        else
        {
            // Validate all the variables
            $r = true;
            
            // Maybe there's an error added by add_error()
            if(count($this->errors)>0)
            {
                $r = false;
            }
            
            // Validate each fields               
            foreach($this->values as $name=>$array)
            {                             
                if(!$this->validate($name, $array))
                {
                    $r = false;                    
                }
            }
            return $r;
        }
    }
    
    /**
    * Return the errors for one or all the variables
    *
    * @param string variable name, could be ommitted if you want all the errors of all the variables
    */
    public function get_errors($name=null)
    {
        if($name)
        {
            if(array_key_exists($name, $this->errors))
            {
                return $this->errors[$name];
            }
            else
            {
                return array();
            }
        }  
        return $this->errors;
    }

    /**
    * Add an error
    *
    * @param string name of the error
    * @param string description of the error
    */
    public function add_error($name, $message)
    {
        $this->errors[$name][] = $message;
    }    

    /**
    * Validate a field 
    */
    private function validate($name, $array)
    {
        $r = true;
        if(count($array))
        {     
            if($array['validators'])
            {
                foreach($array['validators'] as $validator)
                {
                    $error_message = call_user_func($validator['function'], $array['value'], $validator['params']);
                    if($error_message !== null)
                    {
                        $r = false;
                        $this->errors[$name][] = $error_message;
                    }
                }
            }
        }
        return $r;
    }
}
