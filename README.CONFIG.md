# Configuration settings

_If you are reading this then the documentation below should be considered mostly-correct but it still in flux and may have errors._

## Basics

### $GLOBALS['cfg']['enable_feature_api'] = 1;

A boolean flag indicating whether or not the API is available for use.

### $GLOBALS['cfg']['enable_feature_api_documentation'] = 1;

A boolean flag indicating whether or not documentation for API methods is
publicly available.

### $GLOBALS['cfg']['enable_feature_api_logging'] = 1;

A boolean flag indicating whether or not to log API requests.

Currently these are just written to the Apache error logs. Eventually it might
be a configurable thing that allows to store things in Redis, etc. But right now
it's not.

### $GLOBALS['cfg']['enable_feature_api_throttling'] = 0;

This currently doesn't actually do anything. But, you know, throttling! (See
above inre: logging.)

### $GLOBALS['cfg']['api_abs_root_url'] = '';

The fully-qualified URL of the API endpoint.

_You should leave this blank as it will be set automatically in api_config_init()_

### $GLOBALS['cfg']['site_abs_root_url'] = '';

The fully-qualified URL of the site that exposes the API endpoint. These may not
be the same. See the `api_subdomain` parameter below.

_You should leave this blank as it will be set automatically in api_config_init()_

### $GLOBALS['cfg']['api_subdomain'] = '';

Optionally define a sub-domain for your API. For example **api**.my-website.com

### $GLOBALS['cfg']['api_endpoint'] = 'rest/';

Where clients using the API should send requests. Note that this is really
defined (controlled) in the .htaccess file but is included here in order to
generate API documentation.

### $GLOBALS['cfg']['api_require_ssl'] = 1;

A boolean flag indicating whether or not the API requires SSL. This is used to
generate `api_abs_root_url` and `site_abs_root_url`.

## API keys

### $GLOBALS['cfg']['enable_feature_api_require_keys'] = 0;

Irrelevant if you are using OAuth2 (below) otherwise a boolean flag indicating
whether API methods must be called with a registered API key.

### $GLOBALS['cfg']['enable_feature_api_register_keys'] = 1;

A boolean flag indicating whether or not to allow users to create new API keys.

## Delegated authentication

### $GLOBALS['cfg']['enable_feature_api_delegated_auth'] = 1;

A boolean flag indicating whether or not to allow users (and applications) to
create new authentication (access) tokens.

### $GLOBALS['cfg']['api_auth_type'] = 'oauth2';

Currently supported auth types are `oauth2` and `cookies`.

### $GLOBALS['cfg']['enable_feature_api_authenticate_self'] = 1;

A boolean flag indicating whether or not to allow users to magically create both
API keys and auth (access) tokens for themselves without all the usual
hoop-jumping of delegated auth.

# OAuth2

### $GLOBALS['cfg']['api_oauth2_require_authentication_header'] = 0;

An optional flag to require that people pass OAuth2 access tokens around in the
HTTP Authentication header.

### $GLOBALS['cfg']['api_oauth2_allow_get_parameters'] = 1;

Allow people to make OAuth2 requests using the HTTP GET method. There are couple
of things you need to be aware of if you enable this:

* You can (and should) still control whether or not a given method must be
  called using a different HTTP method. See below for details.

* You should not enable this unless you're using HTTPS. You really shouldn't be
  using OAuth2 at all without HTTPS but I can't control that (and by the time
  that the code here might notice the disconnect it will be too late).

## Site keys (and tokens)

Site keys are special API keys that are not bound to any user and are created
with an explicit time to live (ttl). They are retrieved (or created) when the
web site is loaded and checked to make sure they don't need to automatically
expired.

Normally there's no reason for your code to see (or use) site keys. Instead your
code might check a site "token", as in an access token used for delegated
authentication. Site tokens are minted when the page loads and bind the current
site API key with the current user.

That means a separate site token is created for each user and a shared site
token (with limited permissions) is created for logged-out users. Like site keys
the tokens are given a finite time to live (ttl) which is defined below.

All of this allows your website to use its own API the same way every other
application does without having to force users jump through the usual
authentication dance.

### $GLOBALS['cfg']['enable_feature_api_site_keys'] = 1;

A boolean flag controlling whether site keys are enabled.

### $GLOBALS['cfg']['enable_feature_api_site_tokens'] = 1;

A boolean flag controlling whether site tokens are enabled. Note that site
tokens won't work unless site keys are enabled.

### $GLOBALS['cfg']['api_site_keys_ttl'] = 28800;

The time to live for site keys. Default is 28800 seconds, or 8 hours.

### $GLOBALS['cfg']['api_site_tokens_ttl'] = 28000;

The time to live for site tokens for logged-out users. Default is 28800 seconds,
or 8 hours. 

### $GLOBALS['cfg']['api_site_tokens_user_ttl'] = 3600;

The time to live for site tokens for logged-in users. Default is 3600 seconds,
or 1 hours.

## Pagination

### $GLOBALS['cfg']['api_per_page_default'] = 100;

The default number of results to return, per page, for things that are
paginated.

### $GLOBALS['cfg']['api_per_page_max'] = 500;

The maximum number of results to return, per page, for things that are
paginated.

