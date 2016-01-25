# API methods

_If you are reading this then the documentation below should be considered mostly-correct but it still in flux and may have errors._

API methods, and related specifics, are defined as a dictionary where the keys
are method names and the values are the method details.

For example:

	$GLOBALS['cfg']['api']['methods'] => array(
		"api.spec.methods" => array(
			"description" => "Return the list of available API response methods.",
			"documented" => 1,
			"enabled" => 1,
			"library" => "api_spec"
			"parameters" => array(
				array("name" => "method_class", "description" => "Only return methods contained by this method class", "required" => 0),
			),
			"errors" => array(
				array("code" => "404", "description" => "Method class not found"),
				array("code" => "418", "description" => "I'm a teapot"),
			),
			"notes" => array(
				"You are beautiful",
			),
		)
	)

Valid method details include:

### description

A short blurb describing the method.

### documented

A boolean flag indicating whether or not to include this method in the online
(and API based) documentation.

### enabled

A boolean flag indicating whether or not this method can be called.

### library

The name of the library to find the actual function that a method name
corresponds to. All things being equal this is what will be invoked.

### parameters

An optional list of dictionaries containing parameter definitions for the method. Valid keys for each dictionary are:

* **name** - the name of the parameter

* **description** - a short text describing the requirements and context for the parameter

* **required** - a boolean flag indicating whether or not the parameter is required

### errors

An optional list of dictionaries containing error definitions for the method. Valid keys for each dictionary are:

* **code** - the numeric code for the error response

* **description** – a short text describing the reasons or context in which the error was triggered

Error codes are left to the discretion of individual developers.

### notes

An optional list of notes that are each blobs of text.

### request_method

If present then the API dispatching code will ensure that the HTTP method used
to invoke the (API) method matches.

### requires_auth

A boolean flag indicating whether or not a method requires an authorization
token to be passed (and tested).

_This is not necessary to declare if you are using OAuth2._

### requires_crumb

A boolean flag indicating whether or a method requires that a valid (Flamework)
crumb be passed (and validated).

API crumbs are all generated with the same name (`api`) but then appended with a "target" that matches the name of the API method itself. For example:

```
$crumb_api = crumb_generate('api', 'example.helloWorld');
$GLOBALS['smarty']->assign("crumb_api", $crumb_api);
```

### requires_blessing

A boolean flag indicating whether or a method requires that access to the method
be restricted (or "blessed") by API key, access token or host.

API method "blessings" are discussed in detail below.

# API method "defintions"

The `$GLOBALS['cfg']['api_method_definitions']` config variable is a little
piece of syntatic sugar and helper code to keep the growing number of API
methods out of the main config.

This allows to load API methods defined (described above) in separate PHP files
whose naming convention is:

	FLAMEWORK_INCLUDE_DIR . "/config_api_methods.php";

Where the **methods** part is used to denote a particular group of method
definitions. For example:

	$GLOBALS['cfg']['api_method_definitions'] = array(
		'methods',
	);

See the included `config_api_methods.php` for an example of this setup.

# "Blessed" API methods

"Blessed" API methods are those methods with one or more access controls on
them. Access controls are always based on API keys and may also be further
locked down by API access token, host (the remote address of the client calling
the API method) and by Flamework "environment".

API blessings are defined in `$GLOBALS['cfg']['api']['blessings']` setting. For
example:

	$GLOBALS['cfg']['api']['blessings'] => array(
		'xxx-apikey' => array(
			'hosts' => array('127.0.0.1'),
			# 'tokens' => array(),
			# 'environments' => array(),
			'methods' => array(
				'foo.bar.baz' => array(
					'environments' => array('sd-931')
				)
			),
			'method_classes' => array(
				'foo.bar' => array(
					# see above
				)
			),
		),
	);

Each definition is keyed (no pun intended) by an API key whose value is a
dictionary containing some or all of the following properties:

### methods

A list of dictionaries (or hashes) that define the API methods that this key has
been blessed to access.

Each dictionary is keyed by a fully qualified method name whose value is an
array that may be empty or contain one or more of the following properties:
hosts, tokens, environments (described in detail below).

### method_classes

A list of dictionaries (or hashes) that define a group of API methods that this
key has been blessed to access.

Each dictionary is keyed by one or more leading parts of a method name, or a
"class". For example if you wanted to grant access to all the methods in the
`foo.bar` class of methods (`foo.bar.baz`, `foo.bar.helloworld` and so one) to
an API key you would say: 

	'foo.bar' => array()

The value for each key (class) is an array that may be empty or contain one or
more of the following properties: hosts, tokens, environments (described in
detail below).

### hosts

A list of IP addresses (in addition to a specific API key) allowed to access a
given API method.

If defined in the scope of an API key these restrictions will apply to all child
method and method class definitions. Note if this key is defined as a empty list
no requests will be granted access to its corresponding API method.

### tokens

A list of API access tokens (in addition to a specific API key) allowed to
access a given API method.

If defined in the scope of an API key these restrictions will apply to all child
method and method class definitions. Note if this key is defined as a empty list
no requests will be granted access to its corresponding API method.

### environments

A list of Flamework "environment" names (in addition to a specific API key)
allowed to access a given API method. Which means this API method will only be
accessible on a list of servers that self-identify with this name.

Environment names are defined in the `$GLOBALS['cfg']['environment']` setting.

If defined in the scope of an API key these restrictions will apply to all child
method and method class definitions. Note if this key is defined as a empty list
no requests will be granted access to its corresponding API method.

