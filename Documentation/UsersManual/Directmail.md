MK POSTMAN
==========

e-mail marketing system for the TYPO3 CMS TYPO3.

## Directmail

Short manual to configure direct_mail to use MK Postman subscribers as recipients

Set the MK Postman Table as user table in the PageTS

```
mod.web_modules.dmail.userTable = tx_mkpostman_subscribers
```

Create a new recipient list in the direct mail module and chose *Special query* as type.

Next click into the just created list and select *Postman Subscibers* as table.

Be sure that the Disable field was checked as is False and the query looks like this `( disabled != '1')`.

### Unsubscribe

For an unsubscribe link in direct mail, add the following link to your newsletter template
and replace `[MYDOMAIN]` and `[PIDOFSUBSCRIBEPLUGIN]`:

```
<a href="http://[MYDOMAIN]/index.php?id=[PIDOFSUBSCRIBEPLUGIN]&###MKPOSTMAN_UNSUBSCRIBE_PARAMS###">unsubscribe</a>
```