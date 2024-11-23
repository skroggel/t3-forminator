# EXT: forminator
This extension comes with several additional validators, finishers, formElements and general improvements for the usage of the typo3/cms-form-extension, e.g.
* improved ConfirmationMessage- and Email-Finisher
* checkboxes with link-able labels for GDPR or terms & conditions
* improved email-validation
* validator for phone-numbers
* ViewHelper for well formated plaintext-emails

# Installation
Simply install the extension and integrate the TypoScript of the extension into the root page of the website.

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
## AlwaysFalse
The name says it all. This validator always returns false. It is used in the SkipValidation form element.

see: [/Configuration/Yaml/Validators/AlwaysFalse.yaml]()

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

## Hcaptcha
Customized configuration for the use of dreistromland/typo3-hcaptcha together with the extension eliashaeussler/typo3-form-consent

see: [/Configuration/Yaml/FormElements/Hcaptcha.yaml]()

## SingleSelect / MultiSelect
Customized configuration for the use of select form elements with the jQuery framework select2

see: [/Configuration/Yaml/FormElements/SingleSelect.yaml]() & [/Configuration/Yaml/FormElements/MultiSelect.yaml]()

## SkipValidation
In case you need to reload your form because of changes but don't want to validate it, this element is your friend.
Just set the CSS-Class "js-forminator-reload-on-change" to the form-element you want to trigger the reload without validation.
This way you can add fields or change required fields in a convenient way.

see: [/Configuration/Yaml/FormElements/SkipValidation.yaml]()

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

## TextEmail
This form element can be used to add an email field without the standard validation. This allows the use of the BetterEmailAddressValidator integrated here

see: [/Configuration/Yaml/FormElements/TextEmail.yaml]()

# ViewHelpers
## Email/PlaintextLineBreaksViewHelper
With this viewHelper it is possible to generate plaintext mails without having to take care of indentations or line-breaks.
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


## SkipValidationViewHelper
In some cases it is necessary to reload a form to display fields that have dependencies on other fields.
To do this, the form must be reloaded without starting the validation of the form. This is possible with the SkipValidation form element (see above).

This ViewHelper can be used to ensure that the form-fields do not contain any fields with CSS error classes after the form has been reloaded.

Example:
```
<f:form.checkbox
    property="{element.identifier}"
    id="{element.uniqueIdentifier}"
    class="{element.properties.elementClassAttribute}"
    value="{element.properties.value}"
    errorClass="{f:if(condition:'!{forminator:skipValidation(formElement: element)}', then: element.properties.elementErrorClassAttribute)}"
    additionalAttributes="{formvh:translateElementProperty(element: element, property: 'fluidAdditionalAttributes')}"
/>
```

# ExpressionLanguage
## isNull
Sometimes it is necessary to check via a form-configuration (YAML) if a value is null - especially when working with GET-params.
This can be done with this function.

The following example checks if a value is null and then sets the field to readonly.
```
    renderables:
        example:
            identifier: example
            type: Text
            variants:
                do-something:
                    identifier: do-something
                    condition: "isNull(formValues['example'])"
                    properties:
                        fluidAdditionalAttributes:
                            readonly: readonly
```

# JS-Features
## resize-end.js
In some cases, you need to be able to react to the window's resize event. Unfortunately, however, the resize event is not only triggered at the end of the
resizing of the browser window, but every time the size is changed in between. This can lead to an overhead if the event is handled every
the event every time. With the ResizeEnd module, a final event is only triggered when the browser window has been resized.

The script also considers edge-cases such as showing the software-keyboard and showing / hiding the navigation bar on mobile devices
(both trigger a resize event).

### Integration
**Important: The script requires jQuery.**
Integrate the JS file into the page.
```
page {
    includeJSFooterlibs {
        forminatorResizeEnd = EXT:forminator/Resources/Public/JavaScript/resize-end.js
    }
}
```
Then instantiate the resizeEnd-module:
```
$(() => {
  const resizeEnd = new ResizeEnd();
});
```

### Usage
```
$(document).on('madj2k-resize-end', function (e) {
  // triggered on resize-end
});
```

## form.js
The JS module contains
* the initialization of customizable dropdowns with the jQuery plugin select2
* the handling of the resizing of customizable dropdowns with the jQuery plugin select2 in case of a resize-event of the window
* the CSS class “js-forminator-submit-on-change” can be used to submit a form as soon as the value of the field changes (submit-on-change)
* The CSS class “js-forminator-reload-on-change” can be used to submit a form and reload it without validation as soon as the value of the field changes (reload-on-change).

The latter function can be combined well with the SkipValidation function.

### Integration
**Important: The script requires jQuery.**
Integrate the JS-file into the page.
```
page {
    includeJSFooterlibs {
        forminator = EXT:forminator/Resources/Public/JavaScript/forms.js
    }
}
```
Then instantiate the forminator-module:
```
$(() => {
  const forminator = new Forminator();
});
```

### Usage with jQuery select2
**Important: The script requires jQuery.**
Because it is possible to use the forminator without the select2 plugin, the init-functions for select2 have to be called explicitly.

Add the JS-file and CSS-files of select2 into your page:
```
page {

    includeCSSLibs {
        # optional!
        # select2 = EXT:forminator/Resources/Public/Libs/select2-v4-0-13/select2.min.css
    }

    includeJSFooterlibs {
        select2 = EXT:forminator/Resources/Public/Libs/select2-v4-0-13/select2.min.js
        select2Lang = EXT:forminator/Resources/Public/Libs/select2-v4-0-13/i18n/de.js
    }
}
```
Then initialize the select2-functions:
```
$(() => {
  const forminator = new Forminator();
  forminator.initSelect2()
  forminator.initSelect2Resize();
});
```
This extension automatically adds the relevant CSS-Classesfor select2 to work to all select-fields of the form-framework.
However, if you want to use select2 apart from that, just add the following CSS-classes to your select-fields:
* "js-forminator-select2-multiple" for a multi-select
* "js-forminator-select2-single" for a single-select

Example:
```
<f:form.select property="example"
               options="{options}"
               id="example"
               class=form-control js-forminator-select2-single"
```

### Usage with Submit-On-Change
This is useful, for example, in the context of search forms that should automatically update the search results when a field is changed.
To do this, simply add the CSS class “js-forminator-submit-on-change” to the element that is to submit the form in the event of an onChange event.
This function is not necessarily linked to the form framework.

Usage of the CSS-class:
```
<f:form.radio property="example"
              id="example"
              class="form-control js-forminator-submit-on-change"
              value="example"
/>

```

### Usage with Reload-On-Change
Below is an example of its use in combination with SkipValidation.

In this example case, we have a form that offers two options.
If the first option is selected, no information needs to be entered in the text field below.
However, if the second option is selected, the text field below is a mandatory field.
In order to realize this within the form framework, the form is then reloaded using the
CSS-class “js-formiantor-reload-on-change” without triggering the validation of the form.

Usage in a form-configuration (YAML):
```
renderables:
    step-contact:
        identifier: step-contact
        type: Page
        renderables:
            skip-validation:
                type: SkipValidation
                identifier: skip-validation

                options:
                    identifier: options
                    type: SingleSelect
                    properties:
                        options:
                           option-1: 'Option 1'
                           option-1: 'Option 2'
                        elementClassAttribute: "js-forminator-reload-on-change"

                option-details:
                    identifier: option-details
                    type: Text
                    variants:
                        -   identifier: validation-option-details
                            condition: 'traverse(formValues, "options") != "option-1"'
                            validators:
                                -   identifier: NotEmpty
```

# Features for eliashaeussler/typo3-form-consent
If you use eliashaeussler/typo3-form-consent, the forminator comes with some helpful features.
It allows you to override the approval- and dismiss-messages via a flexform that is added to the opt-in-plugin.
The forminator also comes with some helpful templates and partials for this purpose.
The extension also contains a standard routing for the opt-in links.
