<?php

class WP_Response {

	/**
	 * List of messages related to their codes.
	 *
	 * @var array
	 */
	protected $http_statuses = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',                      // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    );

	/**
	 * Response data
	 *
	 * @var string
	 */
    protected $data;

    /**
     * Status code
     *
     * @var integer
     */
    protected $status;

    /**
     * Array of headers
     *
     * @var array
     */
    protected $headers;

    /**
     * Build the response object.
     *
     * @param string
     * @param integer
     * @param array
     */
    public function __construct( $data, $status = 200, $headers = array() )
    {
    	$this->set_data( $data );
		$this->status  = $status;
		$this->headers = $headers;
    }

    /**
     * Called when user returns WP_Response
     *
     * @return string
     */
    public function __toString()
    {
    	$this->set_http_status( $this->status );
    	$this->set_headers( $this->headers );

    	return $this->data;
    }

    /**
     * Returns data of the response.
     *
     * @return string
     */
    public function get()
    {
    	return $this->data;
    }

    /**
     *	Sets data, ensures it can be converted to string.
     *
     * @param mixed
     */
    protected function set_data( $data )
    {
		if ( $data !== null
			&& !is_string( $data )
			&& !is_numeric( $data )
			&& !is_callable( array($data, '__toString') ) )
		{
			throw new \UnexpectedValueException( sprintf('The Response data must be a string or object implementing __toString(), "%s" given.', gettype( $data ) ) );
        }

    	$this->data = (string) $data;
    }

    /**
     *	Loop an array of headers and set them.
     *
     * @param array
     */
    protected function set_headers( $headers )
    {
    	foreach ( $headers as $header )
    	{
    		header( $header );
    	}
    }

    /**
     *	Convert an HTTP status code in a header.
     *
     * @param integer
     */
    protected function set_http_status( $code = 200 )
    {
    	$protocal = 'HTTP/1.0';

    	if ( isset( $_SERVER['SERVER_PROTOCOL'] ) )
    	{
    		$protocal = $_SERVER['SERVER_PROTOCOL'];
    	}

    	$message = $this->http_statuses[$code];

    	header( "{$protocal} {$code} {$message}" );
    }
}
