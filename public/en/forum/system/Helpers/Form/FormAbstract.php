<?php
/**
 * @brief		Abstract Class for input types for Form Builder
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @since		18 Feb 2013
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\Helpers\Form;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Abstract Class for input types for Form Builder
 */
abstract class _FormAbstract
{
	/**
	 * @brief	Name
	 */
	protected $_name = '';
	
	/**
	 * @brief	Label
	 */
	public $label = NULL;
	
	/**
	 * @brief	Default Value
	 */
	public $defaultValue = NULL;
	
	/**
	 * @brief	Value
	 */
	public $value = NULL;
	
	/**
	 * @brief	Unformatted Value
	 */
	public $unformatted = NULL;
	
	/**
	 * @brief	Required?
	 */
	public $required = FALSE;
	
	/**
	 * @brief	Appears Required?
	 */
	public $appearRequired = FALSE;
	
	/**
	 * @brief	Type-Specific Options
	 */
	public $options = array();
	
	/**
	 * @brief	Default Options
	 */
	protected $defaultOptions = array(
		'disabled'	=> FALSE,
	);
	
	/**
	 * @brief	Custom Validation Code
	 */
	protected $customValidationCode;
	
	/**
	 * @brief	Prefix (HTML that displays before the input box)
	 */
	public $prefix;
	
	/**
	 * @brief	Suffix (HTML that displays after the input box)
	 */
	public $suffix;
	
	/**
	 * @brief	HTML ID
	 */
	public $htmlId = NULL;
	
	/**
	 * @brief	Validation Error
	 */
	public $error = NULL;
	
	/**
	 * @brief	Reload form flag (Can be used by JS disabled fall backs to alter form content on submit)
	 */
	public $reloadForm = FALSE;
	
	/**
	 * @brief	Warning
	 */
	public $warningBox = NULL;

	/**
	 * Constructor
	 *
	 * @param	string			$name					Name
	 * @param	mixed			$defaultValue			Default value
	 * @param	bool|NULL		$required				Required? (NULL for not required, but appears to be so)
	 * @param	array			$options				Type-specific options
	 * @param	callback		$customValidationCode	Custom validation code
	 * @param	string			$prefix					HTML to show before input field
	 * @param	string			$suffix					HTML to show after input field
	 * @param	string			$id						The ID to add to the row
	 * @return	void
	 */
	public function __construct( $name, $defaultValue=NULL, $required=FALSE, $options=array(), $customValidationCode=NULL, $prefix=NULL, $suffix=NULL, $id=NULL )
	{
		$this->_name				= $name;
		$this->required				= is_null( $required ) ? FALSE : $required;
		$this->appearRequired		= is_null( $required ) ? TRUE : $required;
		$this->options				= array_merge( $this->defaultOptions, $options );
		$this->customValidationCode	= $customValidationCode;
		$this->prefix				= $prefix;
		$this->suffix				= $suffix;
		$this->defaultValue			= $defaultValue;
		$this->htmlId				= preg_replace( "/[^a-zA-Z0-9\-_]/", "_", $id );
		
		$this->setValue( TRUE );
	}

	/**
	 * Set the value of the element
	 *
	 * @param	bool	$initial	Whether this is the initial call or not. Do not reset default values on subsequent calls.
	 * @return	void
	 */
	public function setValue( $initial=FALSE )
	{
		$name			= $this->name;
		$unlimitedKey	= "{$name}_unlimited";
		$nullKey        = "{$name}_null";
		
		if( mb_substr( $name, 0, 8 ) !== '_new_[x]' and ( mb_strpos( $name, '[' ) ? \IPS\Request::i()->valueFromArray( $name ) !== NULL : ( isset( \IPS\Request::i()->$name ) OR isset( \IPS\Request::i()->$unlimitedKey ) OR isset( \IPS\Request::i()->$nullKey ) ) ) )
		{
			try
			{
				$this->value = $this->getValue();
				$this->unformatted = $this->value;
				$this->value = $this->formatValue();
				$this->validate();
			}
			catch ( \LogicException $e )
			{
				$this->error = $e->getMessage();
			}
		}
		else
		{
			if( $initial )
			{
				$this->value = $this->defaultValue;
				try
				{
					$this->value = $this->formatValue();
				}
				catch ( \LogicException $e )
				{
					$this->error = $e->getMessage();
				}
			}
		}
	}

	/**
	 * Magic get method
	 *
	 * @param	string	$property	Property requested
	 * @return	mixed
	 */
	public function __get( $property )
	{
		if( $property === 'name' )
		{
			return $this->_name;
		}
		
		return NULL;
	}

	/**
	 * Magic set method
	 *
	 * @param	string	$property	Property requested
	 * @param	mixed	$value		Value to set
	 * @return	void
	 * @note	We are operating on the 'name' property so that if an element's name is reset after the element is initialized we can reinitialize the value
	 */
	public function __set( $property, $value )
	{
		if( $property === 'name' )
		{
			$this->_name	= $value;
			$this->setValue();
		}
	}
	
	/**
	 * Get HTML
	 *
	 * @return	string
	 */
	public function __toString()
	{
		return $this->rowHtml();
	}
	
	/**
	 * Get HTML
	 *
	 * @return	string
	 */
	public function rowHtml( $form=NULL )
	{
		try
		{
			if ( $this->label )
			{
				$label = $this->label;
			}
			else
			{
				$label = $this->name;
				if ( isset( $this->options['labelSprintf'] ) )
				{
					$label = \IPS\Member::loggedIn()->language()->addToStack( $label, FALSE, array( 'sprintf' => $this->options['labelSprintf'] ) );
				}
				else if ( isset( $this->options['labelHtmlSprintf'] ) )
				{
					$label = \IPS\Member::loggedIn()->language()->addToStack( $label, FALSE, array( 'htmlsprintf' => $this->options['labelHtmlSprintf'] ) );
				}
				else
				{
					$label = \IPS\Member::loggedIn()->language()->addToStack( $label );
				}
			}

			$desc = $this->name . '_desc';
			$template = \IPS\Theme::i()->getTemplate( 'forms', 'core', 'global' )->rowDesc( $label, $this->html(), $this->appearRequired, $this->error, $this->prefix, $this->suffix, $this->htmlId ?: ( $form ? "{$form->id}_{$this->name}" : NULL ), $this, $form );
			$desc = \IPS\Member::loggedIn()->language()->addToStack( $desc, FALSE, array( 'returnBlank' => TRUE, 'htmlsprintf' => (string) $template, 'flipsprintf' => true ) );
			
			if ( $this->warningBox )
			{
				$warning = $this->warningBox;
			}
			else
			{
				$warning = $this->name . '_warning';
				$template = \IPS\Theme::i()->getTemplate( 'forms', 'core', 'global' )->rowWarning( $label, $this->html(), $this->appearRequired, $this->error, $this->prefix, $this->suffix, $this->htmlId ?: ( $form ? "{$form->id}_{$this->name}" : NULL ), $this, $form );
				$warning = \IPS\Member::loggedIn()->language()->addToStack( $warning, FALSE, array( 'returnBlank' => TRUE, 'htmlsprintf' => (string) $template, 'flipsprintf' => true ) );
			}
			
			/* Some form helpers might want to allow nested and outer suffixes */
			$html = $this->html();

			if( array_key_exists( 'endSuffix', $this->options ) )
			{
				$this->suffix	= $this->options['endSuffix'];
			}

			return \IPS\Theme::i()->getTemplate( 'forms', 'core' )->row( $label, $html, $desc, $warning, $this->appearRequired, $this->error, $this->prefix, $this->suffix, $this->htmlId ?: ( $form ? "{$form->id}_{$this->name}" : NULL ), $this, $form );
		}
		catch ( \Exception $e )
		{
			if ( \IPS\IN_DEV )
			{
				echo '<pre>';
				print_r( $e );
				exit;
			}
			
			throw $e;
		}
	}
	
	/**
	 * Get Value
	 *
	 * @return	mixed
	 */
	public function getValue()
	{
		$name = $this->name;
		return ( mb_strpos( $name, '[' ) OR ( isset( $this->options['multiple'] ) AND $this->options['multiple'] === TRUE ) ) ? \IPS\Request::i()->valueFromArray( $name ) : \IPS\Request::i()->$name;
	}
	
	/**
	 * Format Value
	 *
	 * @return	mixed
	 */
	public function formatValue()
	{
		return $this->value;
	}
	
	/**
	 * Validate
	 *
	 * @throws	\InvalidArgumentException
	 * @return	TRUE
	 */
	public function validate()
	{
		if( $this->value === '' and $this->required )
		{
			throw new \InvalidArgumentException('form_required');
		}
		
		if( $this->customValidationCode !== NULL )
		{
			call_user_func( $this->customValidationCode, $this->value );
		}
		
		return TRUE;
	}
	
	/**
	 * String Value
	 *
	 * @param	mixed	$value	The value
	 * @return	string
	 */
	public static function stringValue( $value )
	{
		if ( is_array( $value ) )
		{
			return implode( ',', array_map( function( $v )
			{
				if ( is_object( $v ) )
				{
					return (string) $v;
				}
				return $v;
			}, $value ) );
		}
		
		return (string) $value;
	}
}