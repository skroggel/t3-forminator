# EXT: forminator
This extension comes with several additional validators, finishers, formElements and general improvements for the usage of the typo3/cms-form-extension, e.g.
* improved ConfirmationMessage- and Email-Finisher
* checkboxes with link-able labels for GDPR or terms & conditions
* improved email-validation
* validator for phone-numbers
* ViewHelper for well formated plaintext-emails

# Finishers
## ConfirmationMessageFinisher
Adds the possibility to use additional fields from the flexForm to display a structured confirmation message.
This can be done by simply using the YAML-configuration using `assignOptions` and FormEditor-configuration.
```
assignOptions:
  - header
  - message
```
see: [/Configuration/Yaml/Finishers/ConfirmationMessage.yaml]()

## EmailToReceiver / EmailToSender
Adds the possibility to use additional fields from the flexForm for usage in the email. This way you can add e.g. a message above the email-content.
This can be done by simply using the YAML-configuration using `assignOptions` and FormEditor-configuration.
```
assignOptions:
  - message
```

see: [/Configuration/Yaml/Finishers/EmailToSender.yaml]() & [/Configuration/Yaml/Finishers/EmailToReceiver.yaml]()

# Validation
## BetterEmailAddressValidator
The email-validator from the core does not check for a FQDN. Thus it is possible to use an e-mail in the format "test@test".
This validator fixes that.

see: [/Configuration/Yaml/Validators/BetterEmailAddress.yaml]()

## PhoneValidators
Checks for valid phone numbers.

see: [/Configuration/Yaml/Validators/BetterEmailAddress.yaml]()

# FormElements
## ConsentContainer / ConsentCheckbox
Adds a container-element and checkboxes for consent-handling (GDPR or terms & conditions).
The labels of the checkboxes can be linked to defined pages and it is possible to a longer description above.

see: [/Configuration/Yaml/FormElements/ConsentCheckbox.yaml]() & [/Configuration/Yaml/FormElements/ConsentContainer.yaml]()

Configuration:
```
consent-container:
    identifier: consent-container
    type: ConsentContainer
    renderables:
        consent-privacy:
            identifier: consent-privacy
            type: ConsentCheckbox
            properties:
                label: ''
                noBadge: 1
                text: '' # set via translation
                privacyPolicyPid: 57
                termsAndConditionsPid: 58
                privacyPolicyLinkText: '' # The link-label in the text, set via translation
                termsAndConditionsLinkText: '' # The link-label in the text, set via translation
                privacyPolicyLinkTextLabel: '' # The link-label in the checkbox-label, set via translation
                termsAndConditionsLinkTextLabel: '' # The link-label in the checkbox-label, set via translation
            validators:
                -   identifier: NotEmpty
```
Translations:
```
<trans-unit id="element.consent-privacy.properties.text">
    <source>By submitting the form, you consent to the processing and use of your data in accordance with the #privacyPolicy# and our #termsAndConditions#.</source>
</trans-unit>
<trans-unit id="element.consent-privacy.properties.privacyPolicyLinkText">
    <source>privacy policy</source>
</trans-unit>
<trans-unit id="element.consent-privacy.properties.termsAndConditionsLinkText">
    <source>terms and conditions</source>
</trans-unit>
<trans-unit id="element.consent-privacy.properties.label">
    <source>I accept the #privacyPolicy# and the </source>
</trans-unit>
<trans-unit id="element.consent-privacy.properties.privacyPolicyLinkTextLabel">
    <source>privacy policy</source>
</trans-unit>

```

## SkipValidation
In case you need to reload your form because of changes but don't want to validate it, this element is your friend.
Just set the CSS-Class "js-forminator-reload-on-change" to the form-element you want to trigger the reload without validation.
This way you can add fields or change required fields in a convenient way.

[/Configuration/Yaml/FormElements/SkipValidation.yaml]()

Configuration:
```
options:
    identifier: options
    type: SingleSelect
    properties:
        options:
           option-1: 'Option 1'
           option-2: 'Option 2'
        elementClassAttribute: "js-forminator-reload-on-change"
```
# ViewHelpers
## Email/PlaintextLineBreaksViewHelper
With this viewHelper ist is possible to generate plaintext mails without having to take care of indentations or line-breaks.
This is often a problem because if you e.g. use conditions in your plaintext mail-templates, the code quickly becomes unreadable.
This viewHelper removes all indentations and line breaks you need for a readable code, but you can set line-breaks manually by simply writing "\n" into your templates.

Usage:
```
<html
	xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
	xmlns:forminator="http://typo3.org/ns/Madj2k/Forminator/ViewHelpers"
	data-namespace-typo3-fluid="true">

    <f:layout name="SystemEmail" />
    <f:section name="Title">{title}</f:section>
    <f:section name="Main"><forminator:email.plaintextLineBreaks>

        Linebreaks and indentations only occur
                where they are needed. \n
            And nowhere else.
    </forminator:email.plaintextLineBreaks></f:section>
</html>
```
Result:
```
Linebreaks and indentations only occur where they are needed.
And nowhere else.
```

## GetElementValueByIdentifier
It does what it names indicates: it returns the value of an formElement you specify by it's identifier.

# eliashaeussler/typo3-form-consent
If you use eliashaeussler/typo3-form-consent, the forminator comes with some helpful features.
It allows you to override the approval- and dismiss-messages via a flexform that is added to the opt-in-plugin.
The forminator also comes with some helpful templates and partials for this purpose.

# typo3/cms-form
It can be a little bit anoying when trying to customize the templates and partials of typo3/cms-form with custom messages.
The included partials in this extension can help you with that.



