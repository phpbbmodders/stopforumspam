# TODO

Ideas not yet built, practical and speculative alike.

## Announce disapprove-and-report on phpBB.com

Disapproving a post from the moderation queue can now report its poster to
Stop Forum Spam in the same step, as requested in
https://www.phpbb.com/customise/db/extension/phpbb_3.1_stop_forum_spam/support/topic/250564
and
https://www.phpbb.com/customise/db/extension/phpbb_3.1_stop_forum_spam/support/topic/222786.
It needs phpBB 3.3.20 or later, which adds the template events it uses
([PHPBB-17737](https://tracker.phpbb.com/browse/PHPBB-17737),
[phpbb/phpbb#7077](https://github.com/phpbb/phpbb/pull/7077)).

Once a release includes it, post this reply in topic 250564 (fill in the
version first):

```
Thanks for the suggestions, and sorry for the slow reply.

Reporting users who never posted: this is possible with two companion extensions.
[list]
[*][url=https://github.com/phpbbmodders/sfscompanion]SFS Companion[/url] has a "Scan users" page in the ACP that checks recent registrations against Stop Forum Spam, with a one-click report and remove. It also adds a "Check SFS" link to member profiles.
[*][url=https://github.com/phpbbmodders/ban-hammer]Ban Hammer[/url] adds a form to member profiles (and to the post approval queue) that can ban the user, remove their content and report them to Stop Forum Spam.
[/list]
Disapprove and report in one step: Stop Forum Spam VERSION adds a "Report to Stop Forum Spam" checkbox to the disapprove screen in the moderation queue, so you can disapprove a post and report its poster at the same time.

William Jacoby (bonelifer)
```

Not planned unless someone asks again: also warning the user from the same
screen. phpBB's warnings are a separate MCP tool.

## Choice of Stop Forum Spam server

Requested on phpBB.com:
https://www.phpbb.com/customise/db/extension/phpbb_3.1_stop_forum_spam/support/topic/206866
https://www.phpbb.com/customise/db/extension/phpbb_3.1_stop_forum_spam/support/topic/247612

The API address is hard-coded to `api.stopforumspam.org` in
`core/sfsapi.php`. Boards in the EU asked to choose `europe.stopforumspam.org`
(or `us.stopforumspam.org`) so lookups stay in their jurisdiction, and to
send a hash of the email address instead of the address itself, which the
SFS API supports.

Planned: an ACP dropdown for the server, defaulting to today's
`api.stopforumspam.org`, and a Yes/No setting for sending the email hash.

Still needs checking: in 2024 a user found `europe.stopforumspam.org`
resolving to a US address, so confirm with Stop Forum Spam where each server
is hosted before describing the option as a GDPR measure.

## Option to allow Tor exit nodes

Requested on phpBB.com:
https://www.phpbb.com/customise/db/extension/phpbb_3.1_stop_forum_spam/support/topic/161161

The SFS API takes a `notorexit` flag that leaves Tor exit nodes out of the
IP check. Add a Yes/No setting for it, off by default so behavior doesn't
change for existing boards.

