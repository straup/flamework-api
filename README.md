flamework-api
==

These are drop-in libraries for adding an API endpoint to a Flamework
project. _It assumes that you are using a current version of [straup's fork of
flamework](https://github.com/straup/flamework)._

It includes libraries and webpages for dispatching API requests and responses as
well as creating and managing API keys.

There is also support for authenticated API methods using cookies (not really
useful for third-party things) and OAuth2 access tokens.

config.php
--

### $GLOBALS['cfg']['api_abs_root_url'] = "https://api.example.com/;

The fully qualified hostname where your API lives. This may or not be the same
as the host your project runs on.

### $GLOBALS['cfg']['enable_feature_api'] = 1;

A boolean flag indicating whether or not the API is available for use.

### $GLOBALS['cfg']['enable_feature_api_documentation'] = 1;

A boolean flag indicating whether or not documentation for API methods is
publicly available.

### $GLOBALS['cfg']['enable_feature_api_logging'] = 1;

A boolean flag indicating whether or not to log API requests.

_Currently these are just written to the Apache error logs._

### $GLOBALS['cfg']['enable_feature_api_throttling'] = 0;

This currently doesn't actually do anything. But, you know, throttling!

### $GLOBALS['cfg']['enable_feature_api_require_keys'] = 0;

Because OAuth2...

### $GLOBALS['cfg']['enable_feature_api_register_keys'] = 1;

A boolean flag indicating whether or not to allow users to create new API keys.

### $GLOBALS['cfg']['enable_feature_api_delegated_auth'] = 1;

A boolean flag indicating whether or not to allow users (and applications) to
create new authentication (access) tokens.

### $GLOBALS['cfg']['api_auth_type'] = 'oauth2';

Currently supported auth types are `oauth2` and `cookies`.

### $GLOBALS['cfg']['enable_feature_api_authenticate_self'] = 1;

A boolean flag indicating whether or not to allow users to magically create both
API keys and auth (access) tokens for themselves without all the usual
hoop-jumping of delegated auth.

### $GLOBALS['cfg']['api_per_page_default'] = 100;

The default number of results to return, per page, for things that are
paginated.

### $GLOBALS['cfg']['api_per_page_max'] = 500;

The maximum number of results to return, per page, for things that are
paginated.

config.api.json
--

API methods, and related specifics, are defined using a JSON config file. A
simple config file looks like this:

For example:

	{
		"default_format": "json",
		"formats": [ "json" ],
		"methods": {
			"api.spec.methods": {
				"description": "Return the list of available API response methods.",
				"documented": 1,
				"enabled": 1,
				"library": "api_spec"
			}
	}

Methods are defined as a hash (or dictionary) of hashes where the keys are
method names and their values are a hash of method-specific details.

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

### requires_auth

A boolean flag indicating whether or not a method requires an authorization
token to be passed (and tested).

_This is not necessary to declare if you are using oauth2_

### requires_crumb

A boolean flag indicating whether or a method requires that a valid (Flamework)
crumb ba passed (and validated).

### request_method

If present then the API dispatching code will ensure that the HTTP method used
to invoke the (API) method matches.

URLs
--

These are the URLs/endpoints that will be added to your project if you install flamework-api.

Take a look at the .htaccess file and pay close attention to all this stuff
that's been commented out at the top. It's a lot of hoop-jumping to separate API
calls (api.example.com/rest) from all the other user-level administrative pages
(example.com/api/methods) and to make sure things that need to be done over SSL
are (like OAuth2).

By default it's all commented out because what do I know about your webserver is
configured. So spend a couple minutes looking at all this stuff and thinking
about it and adjusting accordingly.

Also: Remember that all the security around OAuth2 is predicated around the use
of SSL.

### example.com/api/

A simple landing page for the API with pointers to documentation about methods
and delegated authentication.

### example.com/api/methods/

The list of public (enabled and documented) methods for the API.

### example.com/api/methods/SOME_METHOD_NAME/

Documentation and examples for individual API methods.

### example.com/api/keys/

The list of API keys registered by a (logged in) user.

### example.com/api/keys/register/

Create a new API key.

### example.com/api/keys/API_KEY/

Review or update an existing API key.

### example.com/api/keys/API_KEY/tokens/

The list of OAuth2 access tokens associated with a given API key.

### example.com/api/oauth2/

A simple landing page for the OAuth2 webpages with pointers descriptions 
and pointers.

### example.com/api/oauth2/authenticate/

The standard OAuth2 authenticate a user / authorize an application webpage.

### example.com/api/oauth2/authenticate/like-magic/

A non-standard helper OAuth2 webpage to allow (logged in) users to create
themselves both an API key and a corresponding access token from a single page
by "clicking a button".

### example.com/api/oauth2/authenticate/access_token/

The standard OAuth2 echange a (temporary) grant token for a (more permanent)
access token endpoint. This is meant for robots.

### example.com/api/oauth2/tokens/

A list of OAuth2 access tokens for a (logged in) user.

### example.com/api/oauth2/tokens/API_KEY/

Review of update an existing OAuth2 access token. (Note how we are passing
around the API key in URLs and not the actual access token.)

### example.com/rest/

This is the actual API dispatch/endpoint. Code points here.

See also
--

* [flamework](https://github.com/straup/flamework)

* [flamework-tools](https://github.com/straup/flamework)


