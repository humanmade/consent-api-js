# Altis Consent API

Forks the JavaScript Consent API in [rlankhorst/wp-consent-level-api](https://github.com/rlankhorst/wp-consent-level-api)

## Description

Consent API to read and register the current consent category, allowing consent management plugins and other plugins to work together, improving compliancy.

## What problem does this plugin solve?

Currently it is possibly for a consent management plugin to block third party services like Facebook, Google Maps, Twitter, etc. But if a WordPress plugin places a PHP cookie, a consent management plugin cannot prevent this.

Secondly, there are plugins that integrate tracking code on the clientside in javascript files that, when blocked, break the site.

Or, if such a plugin's javascript is minified, causing the URL to be unrecognizable, it won't get detected by an automatic blocking script.

Lastly, the blocking approach requires a list of all types of URL's that place cookies or use other means of tracking. A generic API where plugins adhere to can greatly
facilitate a webmaster in getting a site compliant.

## Does usage of this API prevent third party services from tracking user data?

Primary this API is aimed at compliant first party cookies or other means of tracking by WordPress plugins. If such a plugin triggers for example Facebook, usage of this API will be of help. If a user manually embeds a facebook iframe, a cookie blocker is needed that initially disables the iframe and or scripts.

Third party scripts have to blocked by a blocking functionality in a consent management plugin. To do this in core would be to intrusive, and is also not applicable to all users: only users with visitors from opt in regions such as the European Union require such a feature. Such a feature also has a risk of breaking things. Additionally, blocking these and showing a nice placeholder, requires even more sophisticated code, all of which should in my opinion not be part of WordPress core, for the same reasons.

That said, the consent API can be used to decide if an iframe or script should be blocked.

## How does it work?

There are two indicators that together tell if consent is given for a certain consent category, e.g. "marketing":

1) the region based `consent_type`, which
can be `optin`, `optout`, or other possible `consent_types`;
2) and the visitor's choice: `not set`, `allow` or `deny`.

The `consent_type` is a function that wraps a filter,`wp_get_consent_type`. If there's no consent management plugin to set it, it will return `false`. This will cause all consent categories to return `true`, allowing cookies and other types of tracking for all categories.

If `optin` is set using this filter, a category will only return `true` if the value of the visitor's choice is `allow`.

If the region based `consent_type` is `optout`, it will return `true` if the visitor's choice is not set or is `allow`.

Clientside, a consent management plugin can dynamically manipulate the consent type, and set the applicable categories.

A plugin can use a hook to listen for changes, or check the value of a given category.

Categories, and most other stuff can be extended with a filter.

## Code Examples

If you have any other code suggestions, please PR them on GitHub!

### javascript, consent management plugin
```javascript
//dynamically set consent type
window.wp_consent_type = 'optin';

//consent management plugin sets cookie when consent category value changes
wp_set_consent('marketing', 'allow');
```

### javascript, cookie placing or tracking plugin
```javascript
//listen to consent change event
document.addEventListener("wp_listen_for_consent_change", function (e) {
  var changedConsentCategory = e.detail;
  for (var key in changedConsentCategory) {
    if (changedConsentCategory.hasOwnProperty(key)) {
      if (key === 'marketing' && changedConsentCategory[key] === 'allow') {
        console.log("just given consent, track user data")
      }
    }
  }
});

//basic implementation of consent check:
if (wp_has_consent('marketing')){
  activateMarketing();
  console.log("set marketing stuff now!");
} else {
  console.log("No marketing stuff please!");
}
```

Frequently asked questions
--------------------------
**Does this plugin block third party services from tracking user data?**

No, this plugin provides a framework through which plugins can know if they are allowed to place cookies or use other means of tracking.
The plugin requires both a consent management plugin for consent management, and a plugin that follows the consent level as can be read from this API.

**How should I go about integrating my plugin?**

Cookies or any other form of local storage can have a function and a purpose. A function is the particular task a cookie has. So a function can be "store the IP adres". Purpose can be seen as the **Why** behind the function. So maybe the IP adres is stored because it is needed for Statistics; or it is stored because it is used for marketing/tracking purposes; or it is needed for functional purposes.

For each function you should consider what the purpose of that function is. There are 5 purpose categories:
functional, statistics-anonymous, statistics, preferences, marketing. These are explained below. Your code should check if consent has been given for the applicable category. If no cookie banner plugin is active,
the Consent API will always return with consent (true).
Please check out the example plugin, and the above code examples.

**What is the difference between the consent categories?**

- statistics:

Cookies or any other form of local storage that are used exclusively for statistical purposes (Analytics Cookies).

- statistics-anonymous:

Cookies or any other form of local storage that are used exclusively for anonymous statistical purposes (Anonymous Analytics Cookies), that are placed on a first party domain, and that do not allow identification of particular individuals.

- marketing:

Cookies or any other form of local storage required to create user profiles to send advertising or to track the user on a website or across websites for simular marketing purposes.

- functional:

The cookie or any other form of local storage is used for the sole purpose of carrying out the transmission of a
communication over an electronic communications network;

OR

The technical storage or access is strictly necessary for the legitimate
purpose of enabling the use of a specific service explicitly requested by the subscriber or
user. If cookies are disabled, the requested functionality will not be available. This makes them essential functional cookies.

- preferences:

Cookies or any other form of local storage that can not be seen as statistics, statistics-anonymous, marketing or functional, and where the technical storage or access is necessary for the legitimate purpose of storing preferences.
