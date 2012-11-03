flamework-api
==

These are drop-in libraries for adding an API endpoint to a flamework
project. It includes libraries and webpages for creating managing API keys as
well as OAuth 2 access tokens.

It also assumes that you are using a current version of [straup's fork of flamework](https://github.com/straup/flamework):

Start with `bin/setup.sh`

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

### example.com/api/oauth2/tokens/ACCESS_TOKEN/

Review of update an existing OAuth2 access token.

### example.com/rest/

This is the actual API dispatch/endpoint. Code points here.

See also
--

* [flamework](https://github.com/straup/flamework)

* [flamework-tools](https://github.com/straup/flamework)


