flamework-api
==

These are drop-in libraries for adding an API endpoint to a flamework
project. It includes libraries and webpages for creating managing API keys as
well as OAuth 2 access tokens.

It also assumes that you are using a current version of [straup's fork of flamework](https://github.com/straup/flamework):

Start with `bin/setup.sh`

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


