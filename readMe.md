# EXT: forminator
This extension comes with several additional validators, finishers, formElements and general improvements for the usage of the typo3/cms-form-extension, e.g.
* improved ConfirmationMessage- and Email-Finisher
* checkboxes with link-able labels for GDPR or terms & conditions
* improved email-validation
* validator for phone-numbers
* ViewHelper for well formated plaintext-emails
* removes the annoying utility-classes (mb-3, mb-2) of the new default layout (layout2) of typo3/cms-form
* several JS functions for forms (reloadOnChange, submitOnChange, AJAX-submit, removal of error classes when user edits fields,...)
* JS-init for select2 elements
* Resize-End-Event for handling the resize event only once on the end

# Installation
Simply install the extension and integrate the TypoScript of the extension into the root page of the website.

# Basics
This extension removes the annoying utility-classes (mb-3, mb-2) of the new default layout (layout2) of typo3/cms-form.
It also adds a basic grid-configuration.

# Extended standard flexform for typo3/cms-form
The extension expands the standard form flexform to include the option of specifying the privacy policy page and the
terms and conditions in the backend. This can be used for the corresponding consent checkboxes.

You can also define your own extensions to the form flexform via the extension configuration in BE (System -> Setup -> Extension Configuration).
This is possible for all forms (*-wildcard) as well as for individual forms.

**_Example:_** Extension of the Flexform when the “my-form” form is selected with the fields defined in the “ExtensionFlexform.xml” file.
```
EXT:example/Resources/Private/Forms/my-form.form.yaml|EXT:example/Configuration/FlexForms/ExtensionFlexform.xml
```

**Important: Extension flexforms require a slightly different structure in order to work.**
An example can be found under [/Configuration/FlexForms/Extend/PrivacyExtend.xml]()

The settings are then added to the renderingOptions of the FormDefintion (via Render.html) and thus can be accessed via the settingsViewHelper:
```
<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
      xmlns:formvh="http://typo3.org/ns/TYPO3/CMS/Form/ViewHelpers"
      xmlns:forminator="http://typo3.org/ns/Madj2k/Forminator/ViewHelpers"
      data-namespace-typo3-fluid="true"
>

    <forminator:settings key="privacyPid"/>

</html>
```

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
You can enable / disable if the international area code is allowed.

```
renderables:
  telephone:
    identifier: telephone
    type: Telephone
    validators:
      - identifier: Phone
        options:
          allowLeadingPlus: 1
```
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
Customized configuration for the use of dreistromland/typo3-hcaptcha and for usage together with the extension eliashaeussler/typo3-form-consent
With the integrated ViewHelper it is also possible to use dreistromland/typo3-hcaptcha in AJAX-forms.

The configruation has to be imported separately, because it is optional.
Just include this snippet in your configuration:
```
imports:
  - { resource: "EXT:forminator/Configuration/Yaml/FormElements/Hcaptcha.yaml" } # hcaptcha
  - { resource: "EXT:forminator/Configuration/Yaml/FormElements/HcaptchaConsent.yaml" } # additional, when hcaptcha plus consent
```

see: [/Configuration/Yaml/FormElements/Hcaptcha.yaml]()

## Salutation
A ready to use select field with typical salutations.

see: [/Configuration/Yaml/FormElements/Salutation.yaml]()

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

## Title
A ready to use select field with typical titles.

see: [/Configuration/Yaml/FormElements/Title.yaml]()

# ViewHelpers
## Configuration/AddToEmailFinisherAsReciever
This ViewHelper can be used to dynamically add further recipients to the finisher configuration of the email finisher after the form has been sent.
dynamically with additional recipients. This is useful if, for example, the form is to be sent to different recipients based on a subject selected in the form.
subject selected in the form is to be sent to different recipients. This ViewHelper can ideally be used together with the Record/GetByFormParamViewHelper.

Example: Individual recipient based on link via contact form

### Step 1
Pass the ID of the contact data record via the GET parameter “contact” and include it as a hidden field
TypoScript:
```
plugin.tx_form {
    settings {
        formDefinitionOverrides {
            your-form {
				renderables {
					step-first {
						renderables {
							contact-uid {
								defaultValue = TEXT
								defaultValue.data = GP:contact
								defaultValue.stdWrap.intval = true
							}
						}
					}
				}
			}
		}
    }
}
```
### Schritt 2
Form YAML with hidden field:
```
type: Form
identifier: your-form
prototypeName: standard

renderables:
  step-first:
    identifier: step-first
    type: Page
    renderables:
      contact-uid:
        identifier: contact-uid
        type: Hidden

finishers:
  emailToReceiver:
    identifier: EmailToReceiver
```

### Schritt 3
The contact data record is now loaded in **Render.html** after the form has been sent
and added as a recipient for the e-mail finisher.
```
<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
      xmlns:formvh="http://typo3.org/ns/TYPO3/CMS/Form/ViewHelpers"
      xmlns:iselContact="http://typo3.org/ns/Multivisio/IselContact/ViewHelpers"
      xmlns:forminator="http://typo3.org/ns/Madj2k/Forminator/ViewHelpers"
      data-namespace-typo3-fluid="true"
>

    <f:variable name="myRecord"
                value="{forminator:record.getByFormParam(
                            formIdentifier: formConfiguration.renderingOptions._originalIdentifier,
                            param: 'contact-uid',
                            table: 'tx_example_domain_model_contact'
                        )}"
    />

    <f:if condition="{myRecord}">
            <f:variable name="formConfiguration"
                        value="{forminator:configuration.addToEmailFinisherAsReceiver(
                                    formConfiguration: formConfiguration,
                                    finisherIdentifier: 'emailToReceiver',
                                    email: myRecord.email,
                                    name: '{myRecord.firstname} {myRecord.lastname}',
                                    override: 1
                                )}"
            />
    </f:if>

    <formvh:render persistenceIdentifier="{settings.persistenceIdentifier}"
                   overrideConfiguration="{
                        finishers: formConfiguration.finishers,
                        renderingOptions: {
                            _settings: settings
                        }
           }"/>
</html>
```

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
                where they are needed.\n
            And nowhere else:\s
            see? you can also add a space at line-end!
    </forminator:email.plaintextLineBreaks></f:section>
</html>
```
Result:
```
Linebreaks and indentations only occur where they are needed.
And nowhere else. see? you can also add a space at line-end!
```

## Record/GetByFormParamViewHelper
This ViewHelper loads a data record from any table based on a form parameter from the request object.
This can be helpful if, for example, you want to select a specific contact address as the recipient for the finisher based on a selection of the topic after the form has been sent.
The relevant data record can then be loaded. The field-parameter is optional. If not set, the whole record is returned as array.

See above!
```
<f:variable name="myRecord"
            value="{forminator:record.getByFormParam(
                        formIdentifier: formConfiguration.renderingOptions._originalIdentifier,
                        param: 'contact-uid',
                        table: 'tx_example_domain_model_test',
                        field: 'title'
                    )}"
/>
```

## Record/GetByIdentifierViewHelper
This ViewHelper loads a data record from any table based on a the value of the given form-field-identifier.
This can be helpful if, for example, you want to display detail information of a database-record based on the value of a field in your form.
The field-parameter is optional. If not set, the whole record is returned as array.
```
<f:variable name="myRecord"
            value="{forminator:record.getByIdentifier(
                        identifier: 'contact-uid',
                        table: 'tx_example_domain_model_test',
                        field: 'title'
                    )}"
/>
```

## Record/GetByUidViewHelper
This ViewHelper loads a data record from any table based on a the value of the given uid.
This can be helpful if, for example, you want to display detail information of a database-record based on a uid.
The field-parameter is optional. If not set, the whole record is returned as array.

```
<f:variable name="myRecord"
            value="{forminator:record.getByIdentifier(
                        uid: 12345,
                        table: 'tx_example_domain_model_test',
                        field: 'title'
                    )}"
/>
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
# Templates
## Checkbox / MultiCheckbox / RadioButton
This extensions adds a span with the CSS-class "form-check-field" around the input[type="radio"] and input[type="checkbox"] that
allows customized styling of the radios / checkboxes with CSS only:

Here an rudimentary example in SASS as prove of concept:
```
.form-check {
    .form-check-field:has(input[type="checkbox"]) {

        position:relative;
        display: inline;
        padding-left: 16px;

        input {
            position: absolute;
            width: 1em;
            height: 1em;
            left:0;

            /* hide it visually, but keep it accessible! */
            opacity: 0;
        }

        &::before {
            content: "";

            position: absolute;
            left: 0

            display: inline-block;
            width: 16px;
            height:  16px;
            border: 1px solid grey;
            margin-right: 10px;
            background-size: 100%;
            background-color: #fff;
            transform: translateY(4px);
        }

        &:has(input:checked),
        &[aria-selected="true"] {
            &::before {
                background-color: #ff0000;
            }
        }
    }
}
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

## forminator.js
The JS module contains
* the initialization of customizable dropdowns with the jQuery plugin select2
* the handling of the resizing of customizable dropdowns with the jQuery plugin select2 in case of a resize-event of the window
* the CSS class “js-forminator-submit-on-change” can be used to submit a form as soon as the value of the field changes (submit-on-change)
* the CSS class “js-forminator-reload-on-change” can be used to submit a form and reload it without validation as soon as the value of the field changes (reload-on-change).
* ajax-submit for forms (requires helhum/typoscript-rendering)
* removal of error classes / error messages on fields when user edits these fields again

### Integration
**Important: The script requires jQuery.**
Integrate the JS-file into the page.
```
page {
    includeJSFooterlibs {
        forminator = EXT:forminator/Resources/Public/JavaScript/forminator.js
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

### Usage of Submit-On-Change
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

### Usage of Reload-On-Change
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

## Usage of Ajax-Submit for forms
**Important: this feature requries helhum/typoscript-rendering!**

You simply have to add an data-action-attribute to your form. This automatically binds the ajax-function
to your form.
```
<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
      xmlns:formvh="http://typo3.org/ns/TYPO3/CMS/Form/ViewHelpers"
      xmlns:typoscriptRendering="http://typo3.org/ns/Helhum/TyposcriptRendering/ViewHelpers"
      data-namespace-typo3-fluid="true"
>

    <formvh:renderRenderable renderable="{form}">
        <formvh:form
            object="{form}"
            action="{form.renderingOptions.controllerAction}"
            method="{form.renderingOptions.httpMethod}"
            id="{form.identifier}"
            section="{form.identifier}"
            enctype="{form.renderingOptions.httpEnctype}"
            addQueryString="{form.renderingOptions.addQueryString}"
            argumentsToBeExcludedFromQueryString="{form.renderingOptions.argumentsToBeExcludedFromQueryString}"
            additionalParams="{form.renderingOptions.additionalParams}"
            additionalAttributes="{formvh:translateElementProperty(element: form, property: 'fluidAdditionalAttributes')}"
            data="{action: '{typoscriptRendering:uri.action(action: form.renderingOptions.controllerAction, controller: \'FormFrontend\')}'}"
            class="form"
        >
    [...]
```

The script automatically scrolls either to the top of the form OR to the first error-field of the form.
You can deactivate this by adding the data-attribute "data-no-scroll-to" to your form.

You can also define a scroll-to-element by adding the data-attribute "data-scroll-to" with an element-id.
In this case the script does not scroll to the top of the form but to the element defined.
However, if there is a form-field with an error-class in the form, this takes precedence.

If you use the default configuration of typo3/cms-form with the new default layout (layout2), this should
work out-of-the-box. If you use custom error-classes you can configure the error-class the script should use.

You also can configure an element which should be considered as offset for the scroll-to-functionality (e.g. a static header).
```
$(() => {
  const forminator = new Forminator({
    'scrollToOffsetId': 'siteheader',
    'formErrorClass': 'is-invalid',
  });
});
```

## Usage of error-class removal on forms when user edits fields
If you use the default configuration of typo3/cms-form with the new default layout (layout2), this should
work out-of-the-box.

However you can configure the relevant error-classes individually.
The only important thing for it to work is that each element needs a the wrapping element with the defined error-class.

```
$(() => {
  const forminator = new Forminator({
    'formElementClass': 'form-element',
    'formErrorClass': 'is-invalid',
    'formGlobalErrorClass': 'is-invalid',
  });
});
```

# Features for eliashaeussler/typo3-form-consent
If you use eliashaeussler/typo3-form-consent, the forminator comes with some helpful features.
It allows you to override the approval- and dismiss-messages via a flexform that is added to the opt-in-plugin.
The forminator also comes with some helpful templates and partials for this purpose.
The extension also contains a standard routing for the opt-in links.
