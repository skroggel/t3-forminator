/*!
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 * Last updated: 16.11.2024
 * v1.0.0
 *
 * This is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

/*
 * Example for initialization:
 *
 * $(() => {
 *  const forminator = new Forminator();
 *  forminator.initSelect2()
 *  forminator.initSelect2Resize();
 * });
 */
class Forminator {

  config = {
    'multiSelectInitClass': 'js-forminator-select2-multiple',
    'singleSelectInitClass': 'js-forminator-select2-single',
    'language': $('html').attr('lang') ?? 'en',
    'dir': 'ltr',
    'submitOnChangeInitClass': 'js-forminator-submit-on-change',
    'reloadOnChangeInitClass': 'js-forminator-reload-on-change',
    'skipValidationInitClass': 'js-forminator-skip-validation',
    'scrollToOffsetId': 'siteheader',
    'formElementClass': 'form-element',
    'formErrorClass': 'is-invalid',
    'formGlobalErrorClass': 'is-invalid',
  };

  resizeEnd = null;

  /**
   * Constructor
   * @param config
   */
  constructor(config) {
    this.config = {...this.config, ...config}
    this.resizeEnd = new ResizeEnd();

    this.initSubmitOnChange();
    this.initReloadOnChange();
    this.initAjaxSubmit();
    this.initRemoveErrorOnChange();
  }

  /**
   * Init select2 fields
   */
  initSelect2() {
    $('.' + this.config.multiSelectInitClass).select2({
      closeOnSelect: false,
      allowClear: true,
      theme: 'multiple-dropdown',
      dir: this.config.dir,
      language: this.config.language,
    });

    $('.' + this.config.singleSelectInitClass).select2({
      allowClear: true,
      dir: this.config.dir,
      language: this.config.language
    });

    // add a special class to the default value
    $('.' + this.config.singleSelectInitClass).on('select2:select', function (e) {
      let data = e.params.data;
      let $renderedSpan = $(this).next('.select2-container').find('.select2-selection__rendered').first();
      if (!data.id) {
        $renderedSpan.addClass('select2-default-value');
      } else {
        $renderedSpan.remove('select2-default-value');
      }
    });
  }

  /**
   * Resize select2 according to new screen size
   */
  initSelect2Resize() {

    let self = this;
    $(document).on('madj2k-resize-end', function (e) {
      $('.' + self.config.multiSelectInitClass).select2('destroy');
      $('.' + self.config.singleSelectInitClass).select2('destroy');
      self.initSelect2();
    });
  }

  /**
   * Submit form on change of field
   */
  initSubmitOnChange() {

    $('.' + this.config.submitOnChangeInitClass).on('change', function (e) {
      let element = $(e.target);
      let form = element.closest('form');

      if (form.length) {
        form.submit();
      }
    });

  }


  /**
   * Reload form on change of field
   */
  initReloadOnChange() {

    let self = this;
    $('.' + this.config.reloadOnChangeInitClass).on('change', function (e) {
      let element = $(e.target);
      let form = element.closest('form');

      if (form.length) {
        form.find(self.config.skipValidationInitClass).val(1);
        form.submit();
      }

    });
  }

  /**
   * Init ajax submit
   */
  initAjaxSubmit() {

    let self = this;
    $(document).on('submit', 'form[data-action]', function (e) {
      e.preventDefault();

      const form = $(this);
      const formId = form.attr('id');
      const action = form.data('action');

      const submitButton = form.find('button:submit:focus');
      const submitButtons = form.find('button:submit');

      const noScrollTo = form.data('no-scroll-to');
      const formScrollToId = form.data('scroll-to');

      let formData = new FormData(form.get(0));
      formData.set(submitButton.attr('Name'), submitButton.val());

      submitButtons.prop('disabled', true).addClass('disabled');
      $(document).trigger('madj2k-before-ajax-submit');

      $.ajax({
        url: action,
        data: formData,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {

          // check if there is a match by id, because it may be the case that more than one form is returned (e.g. with modals)
          const subResult = $('<div>' + result + '</div>').find('#' + formId).first();
          if (subResult) {
            form.html($(subResult).html());
          } else {
            form.replaceWith(result);
          }

          self.initReloadOnChange();
          self.initSubmitOnChange();
          self.initRemoveErrorOnChange();

          const firstError = form.find('.' + self.config.fieldErrorClass + ':first');
          const scrollToElement = form.find('#' + formScrollToId);
          const scrollOffset = $('#' + self.config.scrollToOffsetId).length
            ? $('#' + self.config.scrollToOffsetId).height()
            : 0;
          if (noScrollTo !== 1) {
            if (firstError.length) {
              scrollTo(0, firstError.offset().top - scrollOffset);
            } else if (scrollToElement.length) {
              scrollTo(0, scrollToElement.offset().top - scrollOffset);
            } else {
              scrollTo(0, form.offset().top - scrollOffset);
            }
          }

          submitButtons.prop('disabled', false).removeClass('disabled');
          $(document).trigger('madj2k-after-ajax-submit');
        },
      });
    });
  }

  /**
   * Init removal of error classes on change of field
   */
  initRemoveErrorOnChange() {

    let self = this;
    const removeErrorCssClass = function (element) {
      const elementGroup = $(element).closest('.' + self.config.formElementClass + '.' + self.config.formErrorClass);
      const form = $(element).closest('form');

      if (elementGroup.length) {
        elementGroup
          .removeClass(self.config.formErrorClass)
          .find('.' + self.config.formErrorClass)
          .removeClass(self.config.formErrorClass);
        elementGroup
          .find('.invalid-feedback')
          .remove();
      }
      const errorsLeft = form.find('.' + self.config.formErrorClass);
      if (errorsLeft.length === 0) {
        form
          .find('.' + self.config.formGlobalErrorClass)
          .remove();
      }
    }

    $(document).on('keydown', 'form textarea, form input:not([type="checkbox"]):not([type="radio"])', function (e) {
      removeErrorCssClass(e.target);
    });

    $(document).on('change', 'form select, form input[type="radio"], form input[type="checkbox"]', function (e) {
      removeErrorCssClass(e.target);
    });
  }
}
