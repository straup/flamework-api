# Endpoints

_If you are reading this then the documentation below should be considered mostly-correct but it still in flux and may have errors._

## Basics

These are the URLs/endpoints that will be added to your project if you install flamework-api.

Take a look at the .htaccess file and pay close attention to all this stuff
that's been commented out at the top. It's a lot of hoop-jumping to separate API
calls (api.example.com/rest) from all the other user-level administrative pages
(example.com/api/methods) and to make sure things that need to be done over SSL
are (like OAuth2).

By default it's all commented out because what do I know about your webserver is
configured. So spend a couple minutes looking at all this stuff and thinking
about it and adjusting accordingly.

Also: Remember that _all the security_ around OAuth2 is predicated around the use
of SSL.

### api/

A simple landing page for the API with pointers to documentation about methods
and delegated authentication.

### api/methods/

The list of public (enabled and documented) methods for the API.

### api/methods/SOME_METHOD_NAME/

Documentation and examples for individual API methods.

### api/keys/

The list of API keys registered by a (logged in) user.

### api/keys/register/

Create a new API key.

### api/keys/API_KEY/

Review or update an existing API key.

### api/keys/API_KEY/tokens/

The list of OAuth2 access tokens associated with a given API key.

### api/oauth2/

A simple landing page for the OAuth2 webpages with pointers descriptions 
and pointers.

### api/oauth2/authenticate/

The standard OAuth2 authenticate a user / authorize an application webpage.

### api/oauth2/authenticate/like-magic/

A non-standard helper OAuth2 webpage to allow (logged in) users to create
themselves both an API key and a corresponding access token from a single page
by "clicking a button".

### api/oauth2/authenticate/access_token/

The standard OAuth2 echange a (temporary) grant token for a (more permanent)
access token endpoint. This is meant for robots.

### api/oauth2/tokens/

A list of OAuth2 access tokens for a (logged in) user.

### api/oauth2/tokens/API_KEY/

Review of update an existing OAuth2 access token. (Note how we are passing
around the API key in URLs and not the actual access token.)

### rest/

This is the actual API dispatch/endpoint. Code points here.

# To do

* A good web-based API explorer

* Admin pages for viewing API keys and tokens
