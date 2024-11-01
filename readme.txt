=== SocialHub ===
Contributors: socialhub,patrickdde
Tags: SocialHub,social,integration
Requires at least: 4.6
Tested up to: 6.1
Stable tag: 1.0.9
Requires PHP: 5.6
License: GNU GPL v2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

Integrates WordPress with the SocialHub Software.

== Description ==
SocialHub (https://socialhub.io) is a SaaS for planning and publishing content to social networks as well as community management. Additionally to you Facebook, Twitter and Instagram profiles you are able to integrate you WordPress Blog using this plugin.

Connecting your WordPress Blog with SocialHub will enable you to
- Plan when Posts are going to be published along with your other content in the SocialHub Content Planner Calendar
- Unify your Community Management with SocialHub Inbox which will list Comments from your WordPress Blog along with Comments from your other social channels all within the same Browser Tab: Approve, Reply and Hide comments on your WordPress Blogposts from SocialHub Inbox

== Installation ==
The SocialHub WordPress Plugin is easy to install just like any other. It\'s only important that the WordPress API v2, which is part of the WordPress core functionality, is available and working.

After you added the plugin (either by the Plugins UI within WordPress or by manually downloading, extracting and moving the plugin files to /wp-content/plugins) make sure to enable it by clicking Activate.

You will then find a new menu item called SocialHub in your WordPress Settings (at /wp-admin/options-general.php?page=socialhub-admin).

On this SocialHub Integration settings page you\'ll find a Text Box containing a very long string of characters – this is the Access Token that will give the SocialHub SaaS access to your Blog in order to Integrate it.

Note that this Token will give SocialHub access to WordPress with the User you are currently logged in as. Meaning that all SocialHub will have the same capabilities as your user holds with his role. If you will reply to a comment from the SocialHub Inbox your reply will be created in the name of the user you were logged in with when you copied the Access Token. You might want to consider creating a new user to use for the SocialHub integration.

Copy the Access Token and paste it into the WordPress Channel creation Interface at SocialHub (https://app.socialhub.io/#settings/channels).

SocialHub will immediately check whether your WordPress Sites JSON REST API is publicly reachable and whether the Tokens owner (the WordPress User you where logged in with while copying the Token) has the necessary permissions.

If everything is in order SocialHub will now start by downloading the last 100 Blog Posts and Comments from you WordPress Blog and then it will check for new Content repeatedly to make sure you wont miss out a single interaction with your Community.
