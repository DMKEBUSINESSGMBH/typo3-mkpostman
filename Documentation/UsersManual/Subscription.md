MK POSTMAN
==========

e-mail marketing system for the TYPO3 CMS TYPO3.

Subscription
------------

To use plugins you has to include the static typoscript *MK Postman (Base)* in your template.

For mail subscription there are a MK Postman plugin called *Subscibe Form*.  
You can simply add this by ading a new contentelement,
choosing *MK Postman* in the *Plugins* section of the wizard
and select *Subscibe Form* in the view section.

![MK Postman plugin with subscibe form view](Images/SubscriptionPlugin.png)

Depending to your styles the frontend output should look like this:

![MK Postman plugin with subscibe form view](Images/SubscriptionForm.png)

On subscribe the Data will be stored in the database and a double-opt-in mail will be send automaticaly.

For the e-mail the powerful 
[MK Mailer](https://github.com/DMKEBUSINESSGMBH/typo3-mkmailer/) extension is used.

But at first you has to create the e-mail template by your self!

### Mail Template

For the double-opt-in mail you has to create a MK Mailer Email-Template.

Important: the *Keystring* of the Template has to be *mkpostman_subsciber_activation*.

![MK Postman plugin with subscibe form view](Images/SubscriptionConfirmMailTemplate.png)

In the template currently the following markers available:

 * ###SUBSCRIBER_FIRST_NAME###  
   the first name from the subscribe form
 * ###SUBSCRIBER_LAST_NAME###  
   the last name from the subscribe form
 * ###SUBSCRIBER_EMAIL###   
   the email the subscribe form
 * ###SUBSCRIBER_ACTIVATIONLINKURL###  
   the activation url
 * ###SUBSCRIBER_ACTIVATIONLINK###  
   the linkwrap with tags as wrap

### Form Templating

The subscription form uses the powerful
[MK Forms](https://github.com/DMKEBUSINESSGMBH/typo3-mkforms/) extension.

The form is based on a xml. This form xml is configured by TS and can be overridden:

```
plugin.tx_mkpostman.subscribe.xml = EXT:mkpostman/Resources/Private/Form/Subscribe.xml
```

All formular elements and the template are defined there.

Note, that all fields with the prefix *subscriber-* will be stored in the
table *tx_mkpostman_subscribers*. All other fields will be ignoren on process.
