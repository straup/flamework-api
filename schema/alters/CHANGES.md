### 20150609_db_main.schema

* Add `created_by` and `expires` to ApiKeys

* Add `access_token_secret` and `disabled` to OAuth2AccessTokens

### 20130711_db_main.schema

* Track API key role IDs in the OAuth2AccessTokens table

* Update the `by_user_key` index to OAuth2AccessTokens to account for 'api_key_role_id'

* Update the `by_user` index to OAuth2AccessTokens to account for 'api_key_role_id'

* Add a `by_role_created` index to ApiKeys

### 20130508_db_main.schema

* Add the notion of 'roles' (and by extension a 'role_id') to API keys
